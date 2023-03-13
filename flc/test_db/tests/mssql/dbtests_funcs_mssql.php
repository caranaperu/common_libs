<?php

use flc\database\driver\flcDriver;
use flc\database\driver\mssql\flcMssqlDriver;
use flc\database\flcDbResults;

$system_path = '/var/www/common/flc';

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';


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
                $query->free_result();
            }
        }

    } else {
        print_r($driver->error());
        exit(-1);

    }

}


flcDriver::$dblog_console = true;
//ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
ini_set('sqlsrv.ClientBufferMaxKBSize', '200000'); // Setting to 512M
$driver = new flcMssqlDriver();
$driver->initialize(null, '192.168.18.49', 1532, 'veritrade', 'sa', '202106', $p_charset = 'utf8');
$driver->dbprefix = 'dbo.';


if ($driver->open()) {

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('fn_test_suma', [
        100,
        20
    ]);
    print_results($driver, $query);

    echo PHP_EOL.'----------------------------  Devuelve un solo valor in parameters (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('fn_test_suma', 0, [
            '@mes' => 100,
            '@ano' => 20
        ]);
    print_results($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve un resultset  in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('ufn_get_records', [
        1,
        2020
    ]);
    print_results($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un resultset  in parameters (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('ufn_get_records', 0, [
        '@mes' => 1,
        '@ano' => 2020
    ]);
    print_results($driver, $query);

    $driver->close();
} else {
    echo "Fallo la coneccion";
}

