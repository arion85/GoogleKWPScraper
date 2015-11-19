<?php

$baseDir = dirname(__FILE__) . DIRECTORY_SEPARATOR;
define('DB_CONN_STR', 'host=localhost dbname=parser user=parser password=1111');

$dbconn = pg_connect(DB_CONN_STR)
or die('Не могу подключиться к БД: ' . pg_last_error());

//Получаем состояние парсера и кол-во потоков
$query = 'SELECT status,thr_cnt,last_time FROM params LIMIT 1 OFFSET 0';
$result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
$row = pg_fetch_object($result);

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

while ($row->status == 't') {
    $dbconn = pg_connect(DB_CONN_STR)
    or die('Не могу подключиться к БД: ' . pg_last_error());
    $query = 'UPDATE params SET last_time=' . time();
    pg_query($query) or die('Ошибка запроса: ' . pg_last_error());
    pg_close($dbconn);

    //Сбрасываем "зависшие" состояния
    all_reset($baseDir);

    //Установить максимальное количество процессов и запустить их
    $job->maxProcesses = $row->thr_cnt;
    $job->run();

    //Проверяем состояние парсера и количество потоков
    $dbconn = pg_connect(DB_CONN_STR)
    or die('Не могу подключиться к БД: ' . pg_last_error());
    $query = 'SELECT status,thr_cnt FROM params LIMIT 1 OFFSET 0';
    $result = pg_query($query) or die('Ошибка запроса: ' . pg_last_error());

    $row = pg_fetch_object($result);
    pg_free_result($result);
    pg_close($dbconn);
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
    $dbconn = pg_connect(DB_CONN_STR)
    or die('Не могу подключиться к БД: ' . pg_last_error());

    //Сбросить "занятые" аккаунты
    $query = 'UPDATE accounts SET busy=0 ' .
        "WHERE busy=1";
    pg_query($query) or die('Ошибка запроса: ' . pg_last_error());

    //Сбросить "занятые" прокси
    $query = 'UPDATE proxys SET busy=0 ' .
        "WHERE busy=1";
    pg_query($query) or die('Ошибка запроса: ' . pg_last_error());

    pg_close($dbconn);
}

?>