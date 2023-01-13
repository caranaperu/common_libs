<?php
$system_path = '/var/www/common/framework';
$application_folder = dirname(__FILE__);

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';

use framework\core\accessor\core\model\database\driver\mssql\flcMssqlDriver;
use framework\core\accessor\core\model\database\driver\mysql\flcMysqlDriver;
use framework\core\accessor\core\model\database\driver\postgres\flcPostgresDriver;




$_g_use_pgsql = 'mysql';


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
    $driver->close();
} else {
    echo "Fallo la coneccion";
}

