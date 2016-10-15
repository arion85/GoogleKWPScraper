<?php

class csvParserWorker
{

    public function __construct($baseDir)
    {
        $this->baseDir = $baseDir;
        //Подключаем обработчик csv файлов
        require_once($baseDir . 'libs' . DIRECTORY_SEPARATOR . 'DataSource.php');
    }

    public function run()
    {
        $redis = new Redis();
        $redis->pconnect('127.0.0.1');
        echo("Run csvParser\n");
        $dbconn = mysqli_connect(DB_CONN_HOST, DB_CONN_USER, DB_CONN_PASSW, DB_CONN_DB)
            or die('Не могу подключиться к БД (file:'.__FILE__.' line:'.__LINE__.'): ' . mysqli_connect_error());

        //Выберем все не занятые и включенные аккаунты с привязанным прокси
        $query = 'SELECT * FROM accounts WHERE status=0 AND busy<>1 AND proxy_ip<>0 ';
        $result = mysqli_query($dbconn,$query) or die('Ошибка запроса (file:'.__FILE__.' line:'.__LINE__.'): ' . mysqli_error($dbconn));
        $accounts = pg_fetch_all($result);
        if (count($accounts) < 1) exit();
        pg_free_result($result);

        //Берем рандомный из этого списка
        $acc_arr = array_rand($accounts);

        //Выбранный аккаунт становится занятым
        $query = 'UPDATE accounts SET busy=1 ' .
            "WHERE id={$accounts[$acc_arr]['id']}";
        pg_query($query) or die('Ошибка запроса: ' . pg_last_error());

        //Выберем список работающих прокси
        $query = 'SELECT * FROM proxys WHERE id = ' . $accounts[$acc_arr]['proxy_ip'];
        $result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
        $proxy_acc = pg_fetch_row($result);
        pg_free_result($result);

        //Берем случайный из списка
        $proxy = "{$proxy_acc[1]}:{$proxy_acc[2]}";
        $proxyauth = "{$proxy_acc[3]}:{$proxy_acc[4]}";

        //Получаем страну для которой запущен парсер
        $query = 'SELECT co."ID",co.full_name country, co.short_name cntr_code FROM countries co, params p ' .
            'WHERE co."ID"=p.country_id';
        $result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
        $country = pg_fetch_object($result);
        pg_free_result($result);

        //Берем первый ключ в статусе "В ожидании" (keys_status = 1)
        //Если ключ в статусе "No Keywords" (keys_status = 2) - берем другой
        do {
            $key = $redis->sPop("keys_status:{$country->cntr_code}:1");
        } while ($redis->sIsMember("keys_status:{$country->cntr_code}:2", $key));

        //Увеличим количество использования аккаунта
        $query = 'UPDATE accounts SET cnt_work=cnt_work+1 ' .
            "WHERE id={$accounts[$acc_arr]['id']}";
        pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
        echo("--email={$accounts[$acc_arr]['gm_login']} --pass={$accounts[$acc_arr]['gm_pass']} \n
        --proxy=$proxy --proxy-auth=$proxyauth \n
        --key=$key");
        //Получаем время запуска и запускаем скрипт casperjs
        $start = microtime(true);
        $q = "/usr/local/bin/casperjs {$this->baseDir}googleKeyPlaner.js --proxy=" . $proxy . " --cookies-file={$this->baseDir}cookies/{$key}.txt " .
            "--proxy-auth=" . $proxyauth . " --ignore-ssl-errors=yes --web-security=no --key='{$key}' " .
            "--email={$accounts[$acc_arr]['gm_login']} --pass={$accounts[$acc_arr]['gm_pass']} " .
            "--verphone={$accounts[$acc_arr]['gm_tel']} --country='{$country->country}' > {$this->baseDir}tempFiles/{$key}-exitParser.txt";

        exec($q, $ret_arr, $ret_val);

        //Если casperjs вернул код выхода 100 - нет keywords для данного ключа. Блокируем ключ
        if ($ret_val == 100) {

            //Блокируем ключ со статусом "No keywords" (keys_status = 2)
            $redis->sAdd("keys_status:{$country->cntr_code}:2", $key);

            //Уменьшаем количество использования аккаунта
            $query = 'UPDATE accounts SET cnt_work=cnt_work-1 ' .
                "WHERE id={$accounts[$acc_arr]['id']}";
            pg_query($query) or die('Ошибка запроса: ' . pg_last_error());

            //Останавливаем процесс
            exit();
        }

        //Скачиваем файл по полученной ссылке для ключа
        if (is_file($this->baseDir . 'tempFiles/dnld_' . $key . '.txt')) {
            $dnldUrl = file($this->baseDir . 'tempFiles/dnld_' . $key . '.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            echo('=================Start DownLoad=================');

            $q = "/usr/local/bin/casperjs {$this->baseDir}downloader.js --proxy=" . $proxy . " --cookies-file={$this->baseDir}cookies/{$key}.txt " .
                "--proxy-auth=" . $proxyauth . " --ignore-ssl-errors=yes --web-security=no --key='{$key}' " .
                "--dnldUrl='{$dnldUrl[0]}' > {$this->baseDir}tempFiles/{$key}-exitDownloader.txt";

            exec($q, $ret_arr, $ret_val);
        }

        $work_time = microtime(true) - $start;

        //Проверяем наличие скачанного файла для продолжения
        if (is_file($this->baseDir . 'tempGrab/stats_' . $key . '.csv')) {

            $csvObj = new File_CSV_DataSource();
            $csvObj->settings(array('delimiter' => "\t", 'eol' => ''));
            $csvObj->load($this->baseDir . 'tempGrab/stats_' . $key . '.csv');
            $csvObj->symmetrize(0);

            if (!$csvObj->isSymmetric()) {
                echo('Ошибка! Количество заголовков и столбцов в строках не совпадает в файле tempGrab/stats_' . $key . '.csv');

            }

            //Если файл не пустой
            if ($csvObj->countRows() > 0) {

                $arrForSHA1 = array(0, 0);

                $mainKeyRow = $csvObj->getRow(0);

                //Добавляем информацию для заданного ключа
                $mainKey['keyword'] = ($mainKeyRow[1] != "") ? $mainKeyRow[1] : "";
                $mainKey['AMS'] = ($mainKeyRow[3] != "") ? $mainKeyRow[3] : 0;
                $mainKey['suggested_bid'] = ($mainKeyRow[5] != "") ? $mainKeyRow[5] : 0;

                $redis->delete("key:{$country->cntr_code}:{$key}");
                $redis->hMset("key:{$country->cntr_code}:{$key}", $mainKey);

                $arrForSHA1[0] = sha1(serialize($mainKey));
                //Добавляем keywords для заданного ключа
                $new_words = array();
                $keywordRow = array();
                for ($i = 1; $i < $csvObj->countRows(); $i++) {
                    $childKeyRow = $csvObj->getRow($i);
                    $keywordRow[$i]['keyword'] = ($childKeyRow[1] != "") ? $childKeyRow[1] : "";
                    $keywordRow[$i]['AMS'] = ($childKeyRow[3] != "") ? $childKeyRow[3] : 0;
                    $keywordRow[$i]['suggested_bid'] = ($childKeyRow[5] != "") ? $childKeyRow[5] : 0;
                    //Получаем новые слова для словоря
                    $new_words = array_merge($new_words, explode(" ", $keywordRow[$i]['keyword']));
                }
                //Если есть новые слова для словаря
                if (count($new_words)) {
                    //Удаляем дубликаты
                    $new_words = array_unique($new_words);
                    //Удаляем стоп-слова
                    $query = 'SELECT "stop_words" FROM countries ' .
                        'WHERE "ID"=' . $country->ID;
                    $result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
                    $st_words_str = pg_fetch_object($result)->stop_words;
                    pg_free_result($result);
                    if ($st_words_str) {
                        $st_words_arr = explode("\n", $st_words_str);
                        $new_words = array_diff($new_words, $st_words_arr);
                    }
                    //Если остались слова - продолжаем
                    if (count($new_words) > 0) {
                        //Удаляем однобуквенные слова и слова с запрещенными знаками
                        foreach ($new_words as $w_key => $value) {
                            if (!preg_match('|^[a-z]{2}[a-z0-9_-]*$|i', $value)) {
                                unset($new_words[$w_key]);
                                continue;
                            }
                        }
                        //Добавляем новые слова в словарь со статусом "В ожидании" (keys_status = 1)
                        if (count($new_words) > 0) {
                            $ins_arr = array();
                            foreach ($new_words as $word) {
                                if ($redis->exists("key:{$country->cntr_code}:$word") || $redis->sIsMember("keys_status:{$country->cntr_code}:2", $word)) continue;
                                $redis->sAdd("keys_status:{$country->cntr_code}:1", strtolower(trim($word)));
                            }
                        }
                    }
                }
//!!!! ПРОВЕРИТЬ КЛЮЧ В ХЭШ-ТАБЛИЦЕ !!!!!!!!!!!!!!!!!!!!
                //Добавляем keywords для ключа
                if (count($keywordRow) > 0) {
                    $arrForSHA1[1] = sha1(serialize($keywordRow));
                    $i = 1;
                    $redis->delete($redis->keys("keywords:{$country->cntr_code}:{$key}:*"));
                    foreach ($keywordRow as $row) {
                        $redis->hMset("keywords:{$country->cntr_code}:{$key}:$i", $row);
                        $i++;
                    }
                }

                $redis->delete("keys_hash:{$country->cntr_code}:{$key}");
                $redis->rPush("keys_hash:{$country->cntr_code}:{$key}", $arrForSHA1[0], $arrForSHA1[1]);


            } else {
                //Файл был пустой - разблокируем ключ (keys_status = 1)
                $redis->sAdd("keys_status:{$country->cntr_code}:1", $key);

                //Увеличиваем количество ошибок для аккаунта
                $query = 'UPDATE accounts SET cnt_fail=cnt_fail+1 ' .
                    "WHERE id={$accounts[$acc_arr]['id']}";
                pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
            }
            //Удаляем скачанный файл
            if (is_file($this->baseDir . 'tempGrab/stats_' . $key . '.csv')) {
                unlink($this->baseDir . 'tempGrab/stats_' . $key . '.csv');
            }
        } else {
            //Файл не скачался - разблокируем ключ(keys_status = 1)
            $redis->sAdd("keys_status:{$country->cntr_code}:1", $key);

            //Увеличиваем количество ошибок для аккаунта
            $query = 'UPDATE accounts SET cnt_fail=cnt_fail+1 ' .
                "WHERE id={$accounts[$acc_arr]['id']}";
            pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
        }
        sleep(1);
        pg_close($dbconn);
        echo("STOP Run csvParser\n");
    }
}

?>