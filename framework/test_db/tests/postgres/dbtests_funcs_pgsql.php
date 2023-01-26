<?php

$system_path = '/var/www/common/framework';

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';


use framework\database\driver\flcDriver;
use framework\database\driver\postgres\flcPostgresDriver;
use framework\database\flcDbResults;


function print_results($driver, $query) {
    if ($query) {
        if ($query instanceof flcDbResults) {
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
                    print_r($driver->error());
                }

            }
        } else {
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    print_r($row);
                }
                $query->free_result();;
            }
        }

    } else {
        print_r($driver->error());
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
    ]);
    print_results($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('fn_test_suma',flcDriver::FLCDRIVER_PROCTYPE_SCALAR, [
        'p_id' =>100,
        'p_name'=>20
    ]);
    print_results($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un resultset  in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('getResultset', [

    ]);
    print_results($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un resultset  in parameters (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('getresultset',flcDriver::FLCDRIVER_PROCTYPE_RESULTSET, [
    ]);
    print_results($driver, $query);

    $driver->close();
} else {
    echo "Fallo la coneccion";
}

