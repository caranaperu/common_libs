<?php

use framework\database\driver\flcDriver;
use framework\database\driver\postgres\flcPostgresConnection;
use framework\database\driver\postgres\flcPostgresDriver;

include_once('./flcConnection.php');
include_once('./driver/flcDriver.php');

include_once('./driver/postgres/flcPostgresConnection.php');
include_once('./driver/postgres/flcPostgresDriver.php');

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


$con = new flcPostgresConnection();
$con->initialize(null, '192.168.18.30', 5432, 'db_tests', 'postgres', 'melivane');

if ($con->open()) {
    $driver = new flcPostgresDriver($con);


    echo PHP_EOL.'---------------------------- Devuelve un solo valor ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getSimpleValue', flcDriver::FLCDRIVER_PROCTYPE_SCALAR, [100]/*,[0=>'int']*/);

    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve single resultsets ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve out paramas desde un STORED PROCEDURE ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('assign_demo', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        [1000, flcDriver::FLCDRIVER_PARAMTYPE_INOUT], [
            'en el programa', flcDriver::FLCDRIVER_PARAMTYPE_INOUT
        ]
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets ------------------------'.PHP_EOL;
    // CREATE OR REPLACE FUNCTION getMultipleResultset(who int,ref1 refcursor,ref2 refcursor)

    $query = $driver->execute_stored_procedure('getMultipleResultset', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        3,['ref1',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor'],['ref2',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor']
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets (SP) ------------------------'.PHP_EOL;
    // CREATE OR REPLACE FUNCTION getMultipleResultset(who int,ref1 refcursor,ref2 refcursor)

    $query = $driver->execute_stored_procedure('p_getMultipleResultset', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        3,['ref1',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor'],['ref2',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor']
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets y inout parameter (SP) ------------------------'.PHP_EOL;
    // CREATE OR REPLACE PROCEDURE p_getMultipleResultset_2(who int,ref1 refcursor,ref2 refcursor,inout xx int )

    $query = $driver->execute_stored_procedure('p_getMultipleResultset_2', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        3,['ref1',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor'],['ref2',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor'],[100,flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets y inout parameter (SP) 2 parametros inout  ------------------------'.PHP_EOL;
    // CREATE OR REPLACE PROCEDURE p_getMultipleResultset_3(who int,yy int, ref1 refcursor,ref2 refcursor,inout xx int )

    $query = $driver->execute_stored_procedure('p_getMultipleResultset_3', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        3,400,['ref1',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor'],['ref2',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor'],[100,flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
    ]);
    print_resultsets($driver, $query);



    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets (solo en return no parameters) ------------------------'.PHP_EOL;
    // CREATE OR REPLACE FUNCTION fn_return_data_cursor_2()

    $query = $driver->execute_stored_procedure('fn_return_data_cursor_2', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, null);
    print_resultsets($driver, $query);

    $con->close();
}
