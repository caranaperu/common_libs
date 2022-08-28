<?php

use framework\database\driver\mysql\flcMysqlDriver;

include_once('../../driver/flcDriver.php');
include_once('../../driver/mysql/flcMysqlDriver.php');


$driver = new flcMysqlDriver();
$driver->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

if ($driver->open()) {

    $updstr = $driver->update_string('tb_page', [
        'value' => 'Soy charcol',
        'stuffing' => "'stuffing"
    ], "id LIKE '15000%'", 10, 'order by id');
    echo $updstr.PHP_EOL;

    $insstr = $driver->insert_string('tp_page', [
        'value' => 'Soy charcoal 35',
        'stuffing' => '1234567890'
    ]);
    echo $insstr.PHP_EOL;


    $driver->close();
}
