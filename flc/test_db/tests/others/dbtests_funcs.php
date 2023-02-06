<?php

use flc\database\driver\mssql\flcMssqlDriver;
use flc\database\driver\mysql\flcMysqlDriver;
use flc\database\driver\postgres\flcPostgresDriver;

$system_path = '/var/www/common/flc';
$application_folder = dirname(__FILE__);

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';




$_g_use_pgsql = 'pgsql';


if ($_g_use_pgsql == 'pgsql') {
    $driver = new flcPostgresDriver();
    $driver->initialize(null, '192.168.18.30', 5432, 'db_tests', 'postgres', 'melivane');

} else {
    if ($_g_use_pgsql == 'mysql') {
        $driver = new flcMysqlDriver();
        $driver->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

    } else {
        //ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
        ini_set('sqlsrv.ClientBufferMaxKBSize', '200000'); // Setting to 512M
        $driver = new flcMssqlDriver();
        $driver->initialize(null, '192.168.18.9', 1532, 'veritrade', 'sa', 'melivane', $p_charset = 'utf8');

    }
}

if ($driver->open()) {
    echo 'Host     : '.$driver->get_host().PHP_EOL;
    echo 'Port     : '.$driver->get_port().PHP_EOL;
    echo 'Database : '.$driver->get_database().PHP_EOL;
    echo 'USer     : '.$driver->get_user().PHP_EOL;
    

    $sp_name = $driver->callable_string('test', 'function', 'scalar',[
        'p1', 100, 1, false, true, 44, '44', 2000
    ], [0 => ['string', '(20)'], 2 => 'float', 3 => 'boolean', 5 => 'bit', 6 => 'bit', 7 => 'year']);
    echo $sp_name.PHP_EOL;

    /*    $sp_name = $driver->callable_string('test', 'function', [
            'p2' => 'text', 112 => 'int', 1 => 'bool'
        ]);
        echo $sp_name.PHP_EOL;

        $sp_name = $driver->callable_string('test', 'procedure', [
            'p1' => 'string', 'p2' => 'char', 'p3' => 'enum', 1200 => 'float', 12.25 => 'double', 40, 1 => 'udouble',
            '{"opening":"Sicilian","variations":["pelikan","dragon","najdorf"]}' => 'json', '29121960' => 'date',
            '29121960.00010' => 'datetime'

        ], true);
        echo $sp_name.PHP_EOL;
    */
    $sp_name = $driver->callable_string('test', 'procedure', 'records',[
        'p1', 100, 1, true, false
    ]);
    echo $sp_name.PHP_EOL;

    if ($_g_use_pgsql == 'mssql') {
        // Query 1 , con parametros normales
        $query = $driver->execute_query("select dbo.fn_test_suma(10,20)");
        if (!$query) {
            echo 'Error ';
            $error = $driver->error();
            echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
            exit(-1);
        }
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                print_r($row);
            }
        }
        // Query 4 , con resultset
        $query = $driver->execute_query("select * from dbo.ufn_get_records(1,2020)");
        if (!$query) {
            echo 'Error';
            $error = $driver->error();
            echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
            exit(-1);
        }

        /*while ($row = $query->unbuffered_row()) {
            print_r($row);
        }*/
        echo $query->num_rows().PHP_EOL;
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


    $query = $driver->execute_function('dbo.ufn_get_records', [
        1, 2021,
    ], []);
    if (!$query) {
        echo 'Error ';
        $error = $driver->error();
        echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
        exit(-1);
    }
    if ($query->num_rows() > 0) {
        foreach ($query->result_array() as $row) {
            print_r($row);
        }
    }

    $query = $driver->execute_function('dbo.ufn_sumame', [
        1, 2021,
    ], [0 => 'int']);
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

    $driver->close();
} else {
    echo "Fallo la coneccion";
}

