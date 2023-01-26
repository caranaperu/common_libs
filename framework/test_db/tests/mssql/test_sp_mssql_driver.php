<?php

use framework\database\driver\flcDriver;
use framework\database\driver\mssql\flcMssqlDriver;

$system_path = '/var/www/common/framework';

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';


flcDriver::$dblog_console = true;
//ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
ini_set('sqlsrv.ClientBufferMaxKBSize', '200000'); // Setting to 512M
$driver = new flcMssqlDriver();
$driver->initialize(null, '192.168.18.49', 1532, 'veritrade', 'sa', '202106', $p_charset = 'utf8');


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
                if (is_array($value)) {
                    print_r($value);
                } else {
                    echo 'EL RESULTAOD : '.$key.' - '.$value.PHP_EOL;

                }
            }


        }
    } else {
        print_r($driver->error());
    }

}

if ($driver->open()) {

    echo PHP_EOL.'---------------------------- Devuelve un solo valor ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getSimpleValue', flcDriver::FLCDRIVER_PROCTYPE_SCALAR, [
        [
            100,
            flcDriver::FLCDRIVER_PARAMTYPE_IN,
            'int'
        ]
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un solo valor (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('getSimpleValue', flcDriver::FLCDRIVER_PROCTYPE_SCALAR, ['@_val' => 100]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve single resultsets ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [1, 2018]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve single resultsets (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('getResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        '@_mes' => 1,
        '@ano' => 2018
    ]);
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
        '@_mes' => 1,
        '@ano' => 2018
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getMultipleResultset', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        1,
        2018,
        ['ref1', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor'],
        ['ref2', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor']
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('getMultipleResultset', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        '@_mes' => 1,
        '@ano' => 2018,
        //'ref1' => '',
        //'ref2' => ''
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getMultipleResultset_out', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        1,
        2018,
        [
            '',
            flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ],
        ['ref1', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor'],
        ['ref2', flcDriver::FLCDRIVER_PARAMTYPE_OUT, 'refcursor']
    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params (extended) ------------------------'.PHP_EOL;
    $query = $driver->execute_callable('getMultipleResultset_out', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
        '@_mes' => 1,
        '@ano' => 2018

    ]);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un solo valor - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        [
            100,
            flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]/*,[0=>'int']*/);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un solo valor - out param (2) (extended) ------------------------'.PHP_EOL;
    $query = $driver->execute_callable('assignDemo', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        '@_val' => 100
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        [
            'test',
            flcDriver::FLCDRIVER_PARAMTYPE_INOUT
        ],
        [
            1,
            flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]/*,[0=>'int']*/);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) (extended) ------------------------'.PHP_EOL;
    $query = $driver->execute_callable('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        '@_val' => 'test',
        '@_val2' => 1,
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve 2 valores y un input - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo_3', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        [
            'test',
            flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ],
        333,
        [
            1,
            flcDriver::FLCDRIVER_PARAMTYPE_INOUT
        ]
    ]/*,[0=>'int']*/);
    print_resultsets($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve 2 valores y un input - out param (2) (extended) ------------------------'.PHP_EOL;
    $query = $driver->execute_callable('assignDemo_3', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        '@_val' => 'test',
        '@_valx' => 333,
        '@_val2' => 100

    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Table value parameter test  ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('TVPOrderEntry', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        'Primer',
        [[
            ['0062836700', 367, "2009-03-12", 'AWC Tee Male Shirt', '20.75', null],
            ['1250153272', 256, "2017-11-07", 'Superlight Black Bicycle', '998.45', null],
            ['1328781505', 260, "2010-03-03", 'Silver Chain for Bikes', '88.98', null],
        ],'readonly','TVPParam'],
        [0,flcDriver::FLCDRIVER_PARAMTYPE_INOUT], // OrdNo
        ['',flcDriver::FLCDRIVER_PARAMTYPE_INOUT]  // OrdDate
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Table value parameter test (extended) ------------------------'.PHP_EOL;
    $query = $driver->execute_callable('TVPOrderEntry', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        '@CustID' => 'Primer',
        '@Items' => [
            ['0062836700', 367, "2009-03-12", 'AWC Tee Male Shirt', '20.75', null],
            ['1250153272', 256, "2017-11-07", 'Superlight Black Bicycle', '998.45', null],
            ['1328781505', 260, "2010-03-03", 'Silver Chain for Bikes', '88.98', null],
        ]
    ]);
    print_resultsets($driver, $query);


    echo PHP_EOL.'---------------------------- Table value parameter test  ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('sp_test_boolean_01', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
        [
            1,
            flcDriver::FLCDRIVER_PARAMTYPE_INOUT
        ]
    ]);
    print_resultsets($driver, $query);

    $driver->close();
}
