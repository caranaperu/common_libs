<?php

use framework\database\driver\mysql\flcMysqlDriver;


include_once('../../driver/flcDriver.php');
include_once('../../driver/mysql/flcMysqlDriver.php');

function print_results($driver, $query) {
    if ($query) {
        if ($query->num_rows() > 0) {
            //print_r($query->result_array()[0]);
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


$driver = new flcMysqlDriver();
$driver->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');


if ($driver->open()) {

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('fn_test_suma',  [
            100, 20
    ], /*[[0=>'string','(10)']]*/);
    print_results($driver, $query);


    $driver->close();
} else {
    echo "Fallo la coneccion";
}

