<?php
$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/framework';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);


include_once BASEPATH.'/flcAutoloader.php';


use framework\core\flcServiceLocator;
use framework\database\driver\mysql\flcMysqlDriver;



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
        if ($query->num_rows() > 0) {
            //print_r($query->result_array()[0]);
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

flcServiceLocator::get_instance()->service('log', null, $log_config);


$driver = new flcMysqlDriver();
$driver->initialize(null, '192.168.18.51', 3306, 'db_tests', 'root', '202106');


if ($driver->open()) {

    echo PHP_EOL.'---------------------------- Devuelve un solo valor in parameters ------------------------'.PHP_EOL;

    $query = $driver->execute_function('fn_test_suma', [
        100,
        20
    ], /*[[0=>'string','(10)']]*/);
    print_results($driver, $query);


    $driver->close();
} else {
    echo "Fallo la coneccion";
}

