<?php

use framework\database\driver\postgres\flcPostgresConnection;
use framework\database\driver\postgres\flcPostgresDriver;

include_once('../../driver/flcDriver.php');
include_once('../../driver/postgres/flcPostgresDriver.php');



$driver = new flcPostgresDriver();
$driver->initialize(null, '192.168.18.30', 5432, 'db_tests', 'postgres', 'melivane');

if ($driver->open()) {
    $updstr = $driver->update_string('tb_page', [
        'col2' => "'Soy col2'", 'col3' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
    ], "{$driver->cast_param($driver->escape_identifiers('id'),'string')} like '985%'", 10);
    echo $updstr.PHP_EOL;

    $insstr = $driver->insert_string('tb_page',['id'=>100,'col2'=>'Soy charcoal 35','col3'=>'1234567890']);
    echo $insstr.PHP_EOL;

    $driver->close();
}
