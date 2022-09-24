<?php

$system_path = '/var/www/common/framework';
define('BASEPATH', $system_path);

use framework\database\driver\postgres\flcPostgresDriver;


require_once(BASEPATH.'/database/driver/postgres/flcPostgresDriver.php');

function print_results($driver, $query) {
    if ($query) {
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                print_r($row);
            }
            $query->free_result();;
        }
    } else {
        echo 'Error';
        $error = $driver->error();
        echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
        exit(-1);

    }

}


$driver = new flcPostgresDriver();
$driver->initialize(null, '192.168.18.30', 5432, 'db_tests', 'postgres', 'melivane');


if ($driver->open()) {

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('fn_test_suma',  [
            100, 20
    ]/*,[[0=>'float','(10)']]*/);
    print_results($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un resultset  in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('getResultset',  [

    ]);
    print_results($driver, $query);


    $driver->close();
} else {
    echo "Fallo la coneccion";
}
