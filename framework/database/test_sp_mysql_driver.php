<?php

use framework\database\driver\flcDriver;
use framework\database\driver\mysql\flcMysqlConnection;
use framework\database\driver\mysql\flcMysqlDriver;

include_once('./flcConnection.php');
include_once('./driver/flcDriver.php');

include_once('./driver/mysql/flcMysqlConnection.php');
include_once('./driver/mysql/flcMysqlDriver.php');

function print_resultsets($driver, $query) {
    if ($query) {
        $numresults = $query->get_num_resultsets();

        for ($i = 0; $i < $numresults; $i++) {
            $res = $query->get_resultset_result($i);

            if ($res) {
                echo PHP_EOL.'  --- Resultset -----'.PHP_EOL;

                if ($res->num_rows() > 0) {
                    foreach ($res->result_array() as $row) {
                        print_r($row);
                    }
                    $res->free_result();;
                }

                // PONER COMO HACER FREEEEEEEE
            } else {
                $errors = $driver->error();
                if ($errors) {
                    print_r($errors);
                }
            }

        }

        echo PHP_EOL.'  --- Output parameters -----'.PHP_EOL;

        $outparams = $query->get_out_params();
        if (isset($outparams)) {
            echo PHP_EOL;
            $paramtoshow = $outparams->get_out_params();
            foreach ($paramtoshow as $key => $value) {
                echo 'EL RESULTAOD : '.$key.' - '.$value.PHP_EOL;
            }


        }
    } else {
        print_r($driver->error());
    }

}

$con = new flcMysqlConnection();
$con->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

if ($con->open()) {
    $driver = new flcMysqlDriver($con);
    $query = $driver->execute_stored_procedure_mysql('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_OUTP, [
        ['2', flcDriver::FLCDRIVER_PARAMTYPE_OUT], [100, flcDriver::FLCDRIVER_PARAMTYPE_OUT]
    ]/*,[0=>'int']*/);

    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve un solo valor ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure_mysql('getSingleValue', flcDriver::FLCDRIVER_PROCTYPE_VALUE, [
        [
            100, flcDriver::FLCDRIVER_PARAMTYPE_IN, 'int'
        ]
    ]);

    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve single resultsets ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure_mysql('getResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [1]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve single resultsets y output paramas ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure_mysql('getResultset_out', (flcDriver::FLCDRIVER_PROCTYPE_OUTP | flcDriver::FLCDRIVER_PROCTYPE_RESULTSET), [
        1, 2018, [
            2, flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure_mysql('getMultipleResultset', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        1
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure_mysql('getMultipleResultset_inout', (flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET | flcDriver::FLCDRIVER_PROCTYPE_OUTP), [
        1, [
            1, flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve un solo valor - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure_mysql('assignDemo', flcDriver::FLCDRIVER_PROCTYPE_OUTP, [
        [
            2, flcDriver::FLCDRIVER_PARAMTYPE_INOUT
        ]
    ]/*,[0=>'int']*/);

    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure_mysql('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_OUTP, [
        ['2', flcDriver::FLCDRIVER_PARAMTYPE_OUT],
        [100, flcDriver::FLCDRIVER_PARAMTYPE_OUT]
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve 2 valores y un input - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure_mysql('assignDemo_3', flcDriver::FLCDRIVER_PROCTYPE_OUTP, [
        ['test', flcDriver::FLCDRIVER_PARAMTYPE_OUT],
        333,
        [1, flcDriver::FLCDRIVER_PARAMTYPE_OUT]
    ]);
    print_resultsets($driver, $query);


    $con->close();
}
