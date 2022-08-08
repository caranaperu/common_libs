<?php

use framework\database\driver\flcDriver;
use framework\database\driver\mysql\flcMysqlConnection;
use framework\database\driver\mysql\flcMysqlDriver;

include_once('./flcConnection.php');
include_once('./driver/flcDriver.php');

include_once('./driver/mysql/flcMysqlConnection.php');
include_once('./driver/mysql/flcMysqlDriver.php');


$con = new flcMysqlConnection();
$con->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

if ($con->open()) {
    $driver = new flcMysqlDriver($con);

    $updstr = $driver->update_string('tb_page', [
        'value' => 'Soy charcol', 'stuffing' => "'stuffing"
    ], "id LIKE '16000%'", 10, 'order by id');
    echo $updstr.PHP_EOL;
    $res = $driver->execute_query($updstr);
    if ($res) {
        echo $driver->affected_rows($res).PHP_EOL;
    }


    $insstr = $driver->insert_string('tb_page',['id'=>209,'value'=>'Soy charcoal 35','stuffing'=>'1234567890']);
    echo $insstr.PHP_EOL;
    $res = $driver->execute_query($insstr);
    if ($res) {
        echo $driver->affected_rows($res).PHP_EOL;
    }


    $res =  $driver->execute_query('delete from tb_page where id =209');
    echo $driver->affected_rows($res).PHP_EOL;

    $con->close();
}
