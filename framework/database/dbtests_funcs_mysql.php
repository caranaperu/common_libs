<?php

use framework\database\driver\flcDriver;
use framework\database\driver\mysql\flcMysqlConnection;
use framework\database\driver\mysql\flcMysqlDriver;


include_once('./flcConnection.php');
include_once('./driver/flcDriver.php');


include_once('./driver/mysql/flcMysqlConnection.php');
include_once('./driver/mysql/flcMysqlDriver.php');

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


mysqli_report(MYSQLI_REPORT_ERROR);
$con = new flcMysqlConnection();
$con->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');


if ($con->open()) {
    $driver = new flcMysqlDriver($con);

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('fn_test_suma',  [
            100, 20
    ], /*[[0=>'string','(10)']]*/);
    print_results($driver, $query);


    $con->close();
} else {
    echo "Fallo la coneccion";
}

