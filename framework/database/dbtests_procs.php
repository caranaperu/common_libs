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


$_g_use_pgsql = 'mssql';


if ($_g_use_pgsql == 'pgsql') {
    $con = new flcPostgresConnection();
    $con->initialize(null, '192.168.18.30', 5432, 'db_tests', 'postgres', 'melivane');

} else {
    if ($_g_use_pgsql == 'mysql') {
        $con = new flcMysqlConnection();
        $con->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

    } else {
        //ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '200000'); // Setting to 512M
        $con = new flcMssqlConnection();
        $con->initialize(null, '192.168.18.9', 1532, 'veritrade', 'sa', 'melivane', $p_charset = 'utf8');

    }
}

if ($con->open()) {
    echo 'Host     : '.$con->get_host().PHP_EOL;
    echo 'Port     : '.$con->get_port().PHP_EOL;
    echo 'Database : '.$con->get_database().PHP_EOL;
    echo 'USer     : '.$con->get_user().PHP_EOL;

    if ($_g_use_pgsql == 'pgsql') {
        $driver = new flcPostgresDriver($con);
    } else {
        if ($_g_use_pgsql == 'mysql') {
            $driver = new flcMysqlDriver($con);
        } else {
            $driver = new flcMssqlDriver($con);
        }
    }


    if ($_g_use_pgsql == 'mssql') {
        // Query 1 , con resultset
        $query = $driver->execute_query("{call dbo.getMultipleResultset (1 ,2020)}");
        if (!$query) {
            echo 'Error';
            $error = $driver->error();
            echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
            exit(-1);
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                print_r($row);
            }
        }

        // Query 2 , con retrno de valor simple
        $query = $driver->execute_query("exec dbo.getSimpleValue 100");
        if (!$query) {
            echo 'Error';
            $error = $driver->error();
            echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
            exit(-1);
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                print_r($row);
            }
        }

    } else {
        if ($_g_use_pgsql == 'pgsql') {
            // Query 1 , con parametros normales
            $query = $driver->execute_query("select * from fn_test_suma(10,20)");
            if (!$query) {
                echo 'Error';
                $error = $driver->error();
                echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
                exit(-1);
            }
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    print_r($row);
                }
            }


            // query 2 , con out parameters
            // fn_testcount_outparams(out ecount int , out ocount int)
            //
            $query = $driver->execute_query("select * from fn_testcount_outparams()");
            if (!$query) {
                echo 'Error';
                $error = $driver->error();
                echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
                exit(-1);
            }
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    print_r($row);
                }
            }

            // query 3 , con out,inout y normal parameters
            // fn_test_suma_outparam(p_id integer, inout p_name integer, out p_res integer )
            //
            $query = $driver->execute_query("select * from fn_test_suma_outparam(10::integer,20)");
            if (!$query) {
                echo 'Error';
                $error = $driver->error();
                echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
                exit(-1);
            }
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    print_r($row);
                }
            }
        } else {
            // Query 1 , con parametros normales
            $query = $driver->execute_query("select  fn_test_suma(10,20)");
            if (!$query) {
                echo 'Error';
                $error = $driver->error();
                echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
                exit(-1);
            }
            if ($query->num_rows() > 0) {
                foreach ($query->result_array() as $row) {
                    print_r($row);
                }
            }
        }
    }
    $con->close();
} else {
    echo "Fallo la coneccion";
}

