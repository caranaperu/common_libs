<?php
use flc\database\driver\flcDriver;
use flc\database\driver\mysql\flcMysqlDriver;
use flc\database\flcDbResults;

$system_path = '/var/www/common/flc';

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';


//ejemplo de log config
$log_config = [
    'log_threshold' => 2,
    'log_path' => '',
    'log_file_extension' => '',
    'log_file_permissions' => 0644,
    'log_date_format' => 'Y-m-d H:i:s',
    'log_max_retention' => 30
];

date_default_timezone_set('America/Lima');


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
                //print_r($query->result_array()[0]);
                foreach ($query->result_array() as $row) {
                    print_r($row);
                }
                $query->free_result();;
            }
        }
    } else {
        echo 'Error';
        $error = $driver->error();
        echo 'Error Code = '.$error['code'].' Motivo: '.$error['message'].PHP_EOL;
        exit(-1);

    }

}

//flcServiceLocator::get_instance()->service('log', null, $log_config);


flcDriver::$dblog_console = true;
$driver = new flcMysqlDriver();
$driver->initialize(null, '192.168.18.51', 3306, 'db_tests', 'root', '202106');


if ($driver->open()) {

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('fn_test_suma', [
        100,
        20
    ], /*[[0=>'string','(10)']]*/);
    print_results($driver, $query);


    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('fn_test_suma',0, [
        'a'=>100,
        'b'=>20
    ]);
    print_results($driver, $query);


    echo PHP_EOL.'---------------------------- test bool ------------------------'.PHP_EOL;

    $query = $driver->execute_function('fn_test_bool', [
        true
    ]);
    print_results($driver, $query);

    echo PHP_EOL.'---------------------------- test bool (extended) ------------------------'.PHP_EOL;

    $query = $driver->execute_callable('fn_test_bool', 0, [
        'a'=> true
    ]);
    print_results($driver, $query);


    $driver->close();
} else {
    echo "Fallo la coneccion";
}

