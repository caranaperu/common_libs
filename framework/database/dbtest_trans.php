<?php

use framework\database\driver\mssql\flcMssqlConnection;
use framework\database\driver\mssql\flcMssqlDriver;
use framework\database\driver\mysql\flcMysqlConnection;
use framework\database\driver\mysql\flcMysqlDriver;
use framework\database\driver\postgres\flcPostgresConnection;
use framework\database\driver\postgres\flcPostgresDriver;

include_once('./flcConnection.php');
include_once('./driver/flcDriver.php');

include_once('./driver/postgres/flcPostgresConnection.php');
include_once('./driver/postgres/flcPostgresDriver.php');

include_once('./driver/mysql/flcMysqlConnection.php');
include_once('./driver/mysql/flcMysqlDriver.php');

include_once('./driver/mssql/flcMssqlConnection.php');
include_once('./driver/mssql/flcMssqlDriver.php');

$_g_do_rollback = true;
$_use_pgsql = 'mssql';

if ($_use_pgsql == 'pgsql') {
    $con = new flcPostgresConnection();
    $con->initialize(null, '192.168.18.30', 5432, 'db_tests', 'postgres', 'melivane');

} else if ($_use_pgsql == 'mysql') {
    $con = new flcMysqlConnection();
    $con->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

} else {
    $con = new flcMssqlConnection();
    $con->initialize(null, '192.168.18.9', 1532, 'veritrade_baterias', 'sa', 'melivane', $p_charset = 'utf8');
}


if ($con->open()) {
    echo 'Host     : '.$con->get_host().PHP_EOL;
    echo 'Port     : '.$con->get_port().PHP_EOL;
    echo 'Database : '.$con->get_database().PHP_EOL;
    echo 'USer     : '.$con->get_user().PHP_EOL;

    if ($_use_pgsql == 'pgsql') {
        $driver = new flcPostgresDriver($con);
    } else if ($_use_pgsql == 'mysql') {
        $driver = new flcMysqlDriver($con);
    } else {
        $driver = new flcMssqlDriver($con);
        //$con->sqlsrv_configure("WarningsReturnAsErrors", 1);
    }


    echo 'Iniciando viendo registros existentes'.PHP_EOL;
    $query = $driver->execute_query("SELECT * FROM tb_class");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }
    if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row) {
            print_r($row);
        }
    }

    echo PHP_EOL;
    echo 'Eliminando todos los registros'.PHP_EOL;
    $query = $driver->execute_query("DELETE FROM tb_class");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }
    echo PHP_EOL;
    echo 'Verificando no queden registros'.PHP_EOL;
    $query = $driver->execute_query("SELECT * FROM tb_class");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }

    if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row) {
            print_r($row);
        }
    }
    echo PHP_EOL;
    echo 'Iniciando operaciones de transacciones'.PHP_EOL;

    $driver->set_trans_unique(true);
    $driver->trans_start();

    $query = $driver->execute_query("INSERT INTO tb_class VALUES(1, 'Abhi')");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }
    $query = $driver->execute_query("INSERT INTO tb_class VALUES(2, 'Adam')");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }
    $query = $driver->execute_query("INSERT INTO tb_class VALUES(4, 'Alex')");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }
    $query = $driver->execute_query("INSERT INTO tb_class VALUES(5, 'Rahul')");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }

    // CHECK : porque elimina si no estoy bajo transaccion.
    $driver->trans_complete();

    $driver->trans_start();
    $query = $driver->execute_query("UPDATE tb_class SET name = 'Abhijit' WHERE id = '5'");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }

    $driver->trans_savepoint('A');

    $query = $driver->execute_query("INSERT INTO tb_class VALUES(6, 'Despues de A')");
    if (!$query) {
        echo 'Error';
        //  exit(-1);
    }
    $driver->trans_savepoint('B');

    $query = $driver->execute_query("INSERT INTO tb_class VALUES(7, 'Despues de B')");
    /*  if (!$query) {
          echo 'Error';
          exit(-1);
      }*/
    $driver->trans_savepoint('C');

    $query = $driver->execute_query("INSERT INTO tb_class VALUES(8, 'Despues de C')");
    /*if (!$query) {
        echo 'Error';
        exit(-1);
    }*/

    if ($_g_do_rollback) {
        $driver->trans_rollback('B');
        //$driver->trans_rollback();

    }

    echo 'Iniciando execute sp'.PHP_EOL;

    // Si falla la segunda transaccion perdera todo lo insertado, poner -1 para generar error
    $query = $driver->execute_query("execute sp_test_exception -1,'Con sp 100';");
    //$query = $driver->execute_query("select sp_test_exception( -100,'Con sp 100');");
    //$query = $driver->execute_query("call sp_test_exception( -1,'Con sp 100');");

    if (!$query) {
        echo 'Error ejecutando sp'.PHP_EOL;
        $error = $driver->error();
        echo 'Error Code = ' .$error['code'].' Motivo: '.$error['message'].PHP_EOL;
    }
    echo 'Terminando execute sp'.PHP_EOL;


    $driver->trans_complete();
    print_r($driver->error());

    $query = $driver->execute_query("SELECT * FROM tb_class");
    if (!$query) {
        echo 'Error';
        exit(-1);
    }

    if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row) {
            print_r($row);
        }
    }


    $driver->disconnect();

} else {
    echo "Fallo la coneccion";
}
