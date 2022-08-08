<?php

use framework\database\driver\mssql\flcMssqlConnection;
use framework\database\driver\mssql\flcMssqlDriver;

include_once('./flcConnection.php');
include_once('./driver/flcDriver.php');

include_once('./driver/mssql/flcMssqlConnection.php');
include_once('./driver/mssql/flcMssqlDriver.php');

//ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
ini_set('sqlsrv.ClientBufferMaxKBSize', '200000'); // Setting to 512M
$con = new flcMssqlConnection();
$con->initialize(null, '192.168.18.9', 1532, 'veritrade', 'sa', 'melivane', $p_charset = 'utf8');



if ($con->open()) {
    $driver = new flcMssqlDriver($con);

    $updstr = $driver->update_string('schtest.tb_test', [
        'char_col' => "'Soy charcol224'", 'money_col' => 35.10
    ], "{$driver->cast_param($driver->escape_identifiers('char_col'),'string',null)} like '%charcol%'", 10);
    echo $updstr.PHP_EOL;
    $res = $driver->execute_query($updstr);
    if ($res) {
        echo $driver->affected_rows($res).PHP_EOL;
    }


    $insstr = $driver->insert_string('schtest.tb_test',['id'=>10222,'char_col'=>'Soy charcoal 335','money_col'=>12.88]);
    echo $insstr.PHP_EOL;
    $res = $driver->execute_query($insstr);
    if ($res) {
        echo $driver->affected_rows($res).PHP_EOL;
    }

    $res =  $driver->execute_query('delete from schtest.tb_test where id =10222');
    if ($res) {
        echo $driver->affected_rows($res).PHP_EOL;
    }


    $con->close();
}