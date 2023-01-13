<?php

$system_path = '/var/www/common/framework';

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';


use framework\database\driver\flcDriver;
use framework\database\driver\postgres\flcPostgresDriver;


function print_results($driver, $query) {
    if ($query) {
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                print_r($row);
            }
            $query->free_result();
        }
    } else {
        echo 'Error';
        $error = $driver->error();
        echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
        exit(-1);

    }

}

flcDriver::$dblog_console = true;
$driver = new flcPostgresDriver();
$driver->initialize(null, '192.168.18.51', 5432, 'db_tests', 'postgres', '202106');


if ($driver->open()) {

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('fn_test_suma', [
        100,
        20
    ]/*,[[0=>'float','(10)']]*/);
    print_results($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un resultset  in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('getResultset', [

    ]);
    print_results($driver, $query);


    $callable = $driver->callable_string_extended('getresultset', 'function', 'records');
    echo $callable.PHP_EOL;

    $callable = $driver->callable_string_extended('assigndemo', 'function', 'scalar');
    echo $callable.PHP_EOL;

    //nicio integer, OUT _val character varying, OUT _val2 character varying, OUT _val3 character varying
    $callable = $driver->callable_string_extended('assigndemo', 'function', 'scalar',['inicio'=>0,'_val'=>1]);
    echo $callable.PHP_EOL;

    $driver->close();
} else {
    echo "Fallo la coneccion";
}

