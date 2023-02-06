<?php

use flc\database\driver\flcDriver;
use flc\database\driver\mysql\flcMysqlDriver;

$system_path = '/var/www/common/flc';

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';


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
                    $res->free_result();
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

flcDriver::$dblog_console = true;
$driver = new flcMysqlDriver();
$driver->initialize(null, '192.168.18.51', 3306, 'db_tests', 'root', '202106');

if ($driver->open()) {
    $query = $driver->execute_stored_procedure('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        ['2', flcDriver::FLCDRIVER_PARAMTYPE_OUT],
        [100, flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
    ]/*,[0=>'int']*/);

    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- assignDemo_2 (extended)  ------------------------'.PHP_EOL;
    $query = $driver->execute_callable('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        '_val' => '2',
        '_val2' => 100
    ]);

    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un solo valor ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getSingleValue', flcDriver::FLCDRIVER_PROCTYPE_SCALAR, [
        [
            100,
            flcDriver::FLCDRIVER_PARAMTYPE_IN,
            'int'
        ]
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un solo valor (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('getSingleValue', flcDriver::FLCDRIVER_PROCTYPE_SCALAR, [
        'p_limit' => 100
    ]);

    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve single resultsets ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [2]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve single resultsets (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('getResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, ['p_limit' => 2]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve single resultsets y output paramas ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getResultset_out', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        1,
        2018,
        [
            2,
            flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve single resultsets y output paramas (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('getResultset_out', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        'p_limit' => 1,
        'p_otherparam' => 2018,
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets ------------------------'.PHP_EOL;
    // Also test that refcursors are ignored
    $query = $driver->execute_stored_procedure('getMultipleResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        ['ref0', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor'],
        1,
        ['ref1', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor'],
        ['ref2', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor']
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets (extended) ------------------------'.PHP_EOL;
    // Also test that refcursors are ignored
    $query = $driver->execute_callable('getMultipleResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        'ref0' => 'refcursor', // ignored , only for compatability with pgsql
        'p_limit' => 1,
        'ref1' => 'refcursor', // ignored , only for compatability with pgsql
        'ref2' => 'refcursor' // ignored , only for compatability with pgsql
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params ------------------------'.PHP_EOL;

    // Also test that refcursors are ignored
    $query = $driver->execute_stored_procedure('getMultipleResultset_inout', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        ['ref0', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor'],
        1,
        [
            2,
            flcDriver::FLCDRIVER_PARAMTYPE_INOUT
        ],
        ['ref1', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor'],
        ['ref2', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor']
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params (extended)  ------------------------'.PHP_EOL;

    // Also test that refcursors are ignored
    $query = $driver->execute_callable('getMultipleResultset_inout', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
            'ref0' => 'refcursor', // ignored , only for compatability with pgsql
            'p_limit' => 1,
            'p_inout' => 2,
            'ref1' => 'refcursor',// ignored , only for compatability with pgsql
            'ref2' => 'refcursor',// ignored , only for compatability with pgsql
        ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve un solo valor - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        [
            2,
            flcDriver::FLCDRIVER_PARAMTYPE_INOUT
        ]
    ]/*,[0=>'int']*/);

    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        ['2', flcDriver::FLCDRIVER_PARAMTYPE_INOUT],
        [100, flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) (extended) ------------------------'.PHP_EOL;
    $query = $driver->execute_callable('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        '_val'=>'2',
        '_val2'=>100,
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve 2 valores y un input - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo_3', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        ['test', flcDriver::FLCDRIVER_PARAMTYPE_INOUT],
        333,
        [1, flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve 2 valores y un input - out param (2) (extended) ------------------------'.PHP_EOL;
    $query = $driver->execute_callable('assignDemo_3', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        '_val'=>'test',
        '_val_11'=>333,
        '_val2'=> 1
    ]);
    print_resultsets($driver, $query);

    $driver->close();
}
