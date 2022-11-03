<?php
$system_path = '/var/www/common/framework';
$application_folder = dirname(__FILE__);

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';

use framework\core\accessor\core\model\database\driver\mssql\flcMssqlDriver;
use framework\core\accessor\core\model\database\driver\postgres\flcPostgresDriver;
use framework\core\accessor\core\model\database\driver\mysql\flcMysqlDriver;


$_g_use_pgsql = 'pgsql';


if ($_g_use_pgsql == 'pgsql') {
    $driver = new flcPostgresDriver();
    $driver->initialize(null, '192.168.18.30', 5432, 'db_tests', 'postgres', 'melivane');

} else {
    if ($_g_use_pgsql == 'mysql') {
        $driver = new flcMysqlDriver();
        $driver->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

    } else {
        $driver = new flcMssqlDriver();
        $driver->initialize(null, '192.168.18.9', 1532, 'veritrade_baterias', 'sa', 'melivane', $p_charset = 'utf8');

    }
}

if ($driver->open()) {
    echo 'Host     : '.$driver->get_host().PHP_EOL;
    echo 'Port     : '.$driver->get_port().PHP_EOL;
    echo 'Database : '.$driver->get_database().PHP_EOL;
    echo 'USer     : '.$driver->get_user().PHP_EOL;

    

    $driver->set_trans_unique(false);

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

    echo 'Eliminando todos los registros archivo 2'.PHP_EOL;
    $query = $driver->execute_query("DELETE FROM tb_clas_2");
    if (!$query) {
        echo 'No elimino';
    }
    echo PHP_EOL;
    echo 'Verificando no queden registros archivo 2'.PHP_EOL;
    $query = $driver->execute_query("SELECT * FROM tb_clas_2");
    if (!$query) {
        echo 'No elimino';
    }
    echo 'Iniciando operaciones de transacciones'.PHP_EOL;

    $ret = $driver->trans_start();
    if ($ret) {
        echo 'Inicio transaccion 1'.PHP_EOL;
    }

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

    // In mysql start a transaction always sent a commit first
    // In pg sql no.
    $ret = $driver->trans_start();
    if ($ret) {
        echo 'Inicio transaccion 2'.PHP_EOL;
    } else {
        print_r($driver->error());
    }

    $query = $driver->execute_query("INSERT INTO tb_clas_2 VALUES(1, 'Abhi')");
    if (!$query) {
        echo 'No realizo INSERT INTO tb_clas_2 VALUES(1, "Abhi")';
    }
    $query = $driver->execute_query("INSERT INTO tb_clas_2 VALUES(2, 'Adam')");
    if (!$query) {
        echo 'No realizo INSERT INTO tb_clas_2 VALUES(2, "dam")';
    }

    $ans = $driver->trans_rollback();
    if (!$ans) {
        echo 'No realizo El primer rollback';
    }


    // CHECK : porque elimina si no estoy bajo transaccion.
    // $driver->trans_commit();

    //$driver->trans_rollback();

    $query = $driver->execute_query("INSERT INTO tb_clas_2 VALUES(3, 'Tres')");
    if (!$query) {
        echo 'No realizo INSERT INTO tb_clas_2 VALUES(1, "Abhi")';
    }
    $query = $driver->execute_query("INSERT INTO tb_clas_2 VALUES(4, 'Cuatro')");
    if (!$query) {
        echo 'No realizo INSERT INTO tb_clas_2 VALUES(2, "dam")';
    }

    $ans = $driver->trans_rollback();
    if (!$ans) {
        echo 'No realizo El segundo rollback'.PHP_EOL;
        print_r($driver->error());
    }

    $driver->trans_complete();

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


    $query = $driver->execute_query("SELECT * FROM tb_clas_2");
    if (!$query) {
        echo 'No realizo "SELECT * FROM tb_clas_2';
    } else {
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                print_r($row);
            }
        }

    }


    $driver->disconnect();

} else {
    echo "Fallo la coneccion";
}

