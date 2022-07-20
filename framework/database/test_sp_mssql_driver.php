<?php

use framework\database\driver\flcDriver;
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

    echo PHP_EOL.'---------------------------- Devuelve un solo valor ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getSimpleValue', flcDriver::FLCDRIVER_PROCTYPE_VALUE, [
        [
            100, flcDriver::FLCDRIVER_PARAMTYPE_IN, 'int'
        ]
    ]);
    if ($query) {
        $query = $query->get_resultset_result();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                print_r($row);
            }
        }
    } else {
        print_r($driver->error());
    }

    echo PHP_EOL.'---------------------------- Devuelve single resultsets ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getResultset', flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [1, 2018]);
    if ($query) {
        $res = $query->get_resultset_result();

        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $row) {
                print_r($row);
            }
        }
    } else {
        print_r($driver->error());
    }

    echo PHP_EOL.'---------------------------- Devuelve single resultsets y output paramas ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getResultset_out', (flcDriver::FLCDRIVER_PROCTYPE_OUTP | flcDriver::FLCDRIVER_PROCTYPE_RESULTSET), [
        1, 2018, [
            2, flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]);
    if ($query) {
        $res = $query->get_resultset_result();

        if ($res->num_rows() > 0) {
            foreach ($res->result_array() as $row) {
                print_r($row);
            }
        }

        $outparams = $query->get_out_params();
        if (isset($outparams) > 0) {
            $paramtoshow = $outparams->get_out_params();
            foreach ($paramtoshow as $key => $value) {
                echo 'EL RESULTAOD : '.$key.' - '.$value.PHP_EOL;
            }


        }
    } else {
        print_r($driver->error());
    }

    echo PHP_EOL.'---------------------------- Devuelve multiple resultsets ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getMultipleResultset', flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [1, 2018]);
    if ($query) {
        for ($i=0 ; $i < $query->get_num_resultsets(); $i++) {
            $res = $query->get_resultset_result($i);

            if ($res->num_rows() > 0) {
                foreach ($res->result_array() as $row) {
                    print_r($row);
                }
            }

        }

    } else {
        print_r($driver->error());
    }

    echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params ------------------------'.PHP_EOL;

    $query = $driver->execute_stored_procedure('getMultipleResultset_out', (flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET | flcDriver::FLCDRIVER_PROCTYPE_OUTP),
        [1, 2018,['',flcDriver::FLCDRIVER_PARAMTYPE_OUT]]);
    if ($query) {
        for ($i=0 ; $i < $query->get_num_resultsets(); $i++) {
            $res = $query->get_resultset_result($i);

            if ($res->num_rows() > 0) {
                foreach ($res->result_array() as $row) {
                    print_r($row);
                }
            }

        }
        $outparams = $query->get_out_params();
        if (isset($outparams) > 0) {
            $paramtoshow = $outparams->get_out_params();
            foreach ($paramtoshow as $key => $value) {
                echo 'EL RESULTAOD : '.$key.' - '.$value.PHP_EOL;
            }


        }

    } else {
        print_r($driver->error());
    }

    echo PHP_EOL.'---------------------------- Devuelve un solo valor - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo', flcDriver::FLCDRIVER_PROCTYPE_OUTP, [
        [
            100, flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]/*,[0=>'int']*/);

    if ($query) {
        $res = $query->get_resultset_result();

        if ($res && $res->num_rows() > 0) {
            foreach ($res->result_array() as $row) {
                print_r($row);
            }
        }

        $outparams = $query->get_out_params();
        if (isset($outparams)) {
            $paramtoshow = $outparams->get_out_params();
            foreach ($paramtoshow as $key => $value) {
                echo 'EL RESULTAOD : '.$key.' - '.$value.PHP_EOL;
            }


        }
    } else {
        echo 'Error';
        $error = sqlsrv_errors();
        print_r($error).PHP_EOL;
    }


    echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo_2', flcDriver::FLCDRIVER_PROCTYPE_OUTP, [
        [
            'test', flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ], [
            1, flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]/*,[0=>'int']*/);

    if ($query) {

        $res = $query->get_resultset_result();

        if ($res && $res->num_rows() > 0) {
            foreach ($res->result_array() as $row) {
                print_r($row);
            }
        }

        $outparams = $query->get_out_params();
        if (isset($outparams)) {
            $paramtoshow = $outparams->get_out_params();
            foreach ($paramtoshow as $key => $value) {
                echo 'EL RESULTAOD : '.$key.' - '.$value.PHP_EOL;
            }


        }
    } else {
        echo 'Error';
        $error = sqlsrv_errors();
        print_r($error).PHP_EOL;
    }

    echo PHP_EOL.'---------------------------- Devuelve 2 valores y un input - out param (2) ------------------------'.PHP_EOL;
    $query = $driver->execute_stored_procedure('assignDemo_3', flcDriver::FLCDRIVER_PROCTYPE_OUTP, [
        [
            'test', flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ], 333, [
            1, flcDriver::FLCDRIVER_PARAMTYPE_OUT
        ]
    ]/*,[0=>'int']*/);

    if ($query) {
        $res = $query->get_resultset_result();

        if ($res && $res->num_rows() > 0) {
            foreach ($res->result_array() as $row) {
                print_r($row);
            }
        }

        $outparams = $query->get_out_params(1);
        if (isset($outparams)) {
            $paramtoshow = $outparams->get_out_params();
            foreach ($paramtoshow as $key => $value) {
                echo 'EL RESULTAOD : '.$key.' - '.$value.PHP_EOL;
            }


        }
    } else {
        echo 'Error';
        $error = sqlsrv_errors();
        print_r($error).PHP_EOL;
    }

    $con->close();
}
