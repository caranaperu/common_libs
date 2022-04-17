<?php

use framework\database\driver\postgres\flcPostgresConnection;
use framework\database\driver\postgres\flcPostgresDriver;

include_once('./flcConnection.php');
//include_once('./flcDbResult.php');
include_once('./driver/flcDriver.php');

include_once('./driver/postgres/flcPostgresConnection.php');
include_once('./driver/postgres/flcPostgresDriver.php');

/*
$dsn = 'postgres://arana:mipassw@localhost:8080/midb?parameter=2';

$p = parse_url($dsn);
print_r($p);

$port = parse_url($dsn, PHP_URL_PORT);
print_r('El port es : '.$port);*/


$con = new flcPostgresConnection();
$con->initialize(null,'192.168.18.30',5432,'db_flabsregs','postgres','melivane');
if ($con->open()) {
    $driver = new flcPostgresDriver($con);
    $RES = $driver->execute_query("select * from tb_sys_menu");

    if ($RES && $RES->num_rows() > 0) {
        echo 'Hay registros'.PHP_EOL;
        $results  = $RES->result_array();
        if ($results && count($results)) {
            echo 'Se leyeron'.PHP_EOL;
        }
        /*
        foreach ($RES->result_array() as $row) {
            //Seteamos los valores de la base en el modelo.
            $results[] = $row;
            $row = null;
        }*/
    }

    $con->close();

} else {
    echo "Fallo la coneccion";
}

