<?php

$baseDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
define('DB_CONN_HOST', 'localhost');
define('DB_CONN_DB', 'parser');
define('DB_CONN_USER', 'parser');
define('DB_CONN_PASSW', 'parser');

$dbconn = mysqli_connect(DB_CONN_HOST, DB_CONN_USER, DB_CONN_PASSW, DB_CONN_DB)
    or die('Не могу подключиться к БД (line:'.__LINE__.'): ' . mysqli_connect_error());

//Получаем состояние парсера и кол-во потоков
$query = 'SELECT status,thr_cnt,last_time FROM params LIMIT 1 OFFSET 0';
$result = mysqli_query($dbconn,$query) or die('Ошибка запроса: ' . mysqli_error($dbconn));
$row = mysqli_fetch_object($result);

if(!$row){
    die('Ошибка! Необходимо сохранить настройки парсера на странице http://{your_domain}/configurator/admin/main');
}
/*if($row->last_time && time()-$row->last_time<125){
    pg_free_result($result);
    pg_close($dbconn);
    echo('NotTime');
    exit();
}*/

require $baseDir . "classes/jobDaemon.php";
require $baseDir . "classes/csvParserWorker.php";

//declare(ticks = 1);

$job = new JobDaemon($baseDir);

while ($row->status == true) {
    if(!$dbconn){
        $dbconn = mysqli_connect(DB_CONN_HOST, DB_CONN_USER, DB_CONN_PASSW, DB_CONN_DB)
            or die('Не могу подключиться к БД (line:'.__LINE__.'): ' . mysqli_connect_error());
    }

    $query = 'UPDATE params SET last_time=' . time();
    mysqli_query($dbconn,$query) or die('Ошибка запроса (line: '.__LINE__.'): ' . mysqli_error($dbconn));
    mysqli_close($dbconn);

    //Сбрасываем "зависшие" состояния
    all_reset($baseDir);

    //Установить максимальное количество процессов и запустить их
    $job->maxProcesses = $row->thr_cnt;
    $job->run();

    //Проверяем состояние парсера и количество потоков
    if(!$dbconn){
        $dbconn = mysqli_connect(DB_CONN_HOST, DB_CONN_USER, DB_CONN_PASSW, DB_CONN_DB)
            or die('Не могу подключиться к БД (line:'.__LINE__.'): ' . mysqli_connect_error());
    }
    $query = 'SELECT status,thr_cnt FROM params LIMIT 1 OFFSET 0';
    $result = mysqli_query($dbconn,$query) or die('Ошибка запроса (line:'.__LINE__.'): ' . mysqli_error($dbconn));

    $row = mysqli_fetch_object($result);
    mysqli_free_result($result);
    mysqli_close($dbconn);
}

function all_reset($baseDir)
{
    //Очищаем временную папку temp
    if (file_exists($baseDir . 'tempFiles'))
        foreach (glob($baseDir . 'tempFiles/*') as $file)
            unlink($file);

    //Очищаем временную папку для граббера
    if (file_exists($baseDir . 'tempGrab'))
        foreach (glob($baseDir . 'tempGrab/*') as $file)
            unlink($file);

    //Очищаем временную папку для куков
    if (file_exists($baseDir . 'cookies'))
        foreach (glob($baseDir . 'cookies/*') as $file)
            unlink($file);

    //Приводим все ключи "в обработке" к состоянию "в ожидании"
    $dbconn = mysqli_connect(DB_CONN_HOST, DB_CONN_USER, DB_CONN_PASSW, DB_CONN_DB)
        or die('Не могу подключиться к БД (line:'.__LINE__.'): ' . mysqli_connect_error());

    //Сбросить "занятые" аккаунты
    $query = 'UPDATE accounts SET busy=0 WHERE busy=1';
    mysqli_query($dbconn,$query) or die('Ошибка запроса (line:'.__LINE__.'): ' . mysqli_error($dbconn));

    //Сбросить "занятые" прокси
    $query = 'UPDATE proxys SET busy=0 WHERE busy=1';
    mysqli_query($dbconn,$query) or die('Ошибка запроса (line:'.__LINE__.'): ' . mysqli_error($dbconn));

    mysqli_close($dbconn);
}

?>