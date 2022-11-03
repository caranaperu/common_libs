<?php
$system_path = '/var/www/common/framework';

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';

use framework\core\accessor\core\model\database\driver\postgres\flcPostgresConnection;
use framework\core\accessor\core\model\database\driver\postgres\flcPostgresDriver;


$driver = new flcPostgresDriver();
$driver->initialize(null, '192.168.18.30', 5432, 'db_tests', 'postgres', 'melivane');

if ($driver->open()) {

    $updstr = $driver->update_string('tb_page', [
        'col2' => "'Soy col32'", 'col3' => 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'
    ], "{$driver->cast_param($driver->escape_identifiers('id'),'string')} like '985%'", 10);
    echo $updstr.PHP_EOL;
    $res = $driver->execute_query($updstr);
    if ($res) {
        echo $res->affected_rows().PHP_EOL;
    }


    $insstr = $driver->insert_string('tb_page',['id'=>1001,'col2'=>'Soy charcoal 35','col3'=>'1234567890']);
    echo $insstr.PHP_EOL;
    $res = $driver->execute_query($insstr);
    if ($res) {
        echo $res->affected_rows().PHP_EOL;
    }

    $res =  $driver->execute_query('delete from tb_page where id =1001');
    echo $res->affected_rows().PHP_EOL;

    $cols = $driver->primary_key('tb_page');
    if ($cols) {
        print_r($cols);
    }

    $driver->close();
}
