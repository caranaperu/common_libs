<?php

use framework\database\driver\mssql\flcMssqlDriver;


require_once('../../driver/flcDriver.php');
require_once('../../driver/mssql/flcMssqlDriver.php');

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


//ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
ini_set('sqlsrv.ClientBufferMaxKBSize', '200000'); // Setting to 512M
$driver = new flcMssqlDriver();
$driver->initialize(null, '192.168.18.9', 1532, 'veritrade', 'sa', 'melivane', $p_charset = 'utf8');


if ($driver->open()) {

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('dbo.fn_test_suma',  [
            100, 20
    ]);
    print_results($driver, $query);

    echo PHP_EOL.'---------------------------- Devuelve un resultset  in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('dbo.ufn_get_records',  [
        1, 2020
    ]);
    print_results($driver, $query);


    $driver->close();
} else {
    echo "Fallo la coneccion";
}

