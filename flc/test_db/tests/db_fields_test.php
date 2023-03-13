<?php
namespace flc\test_db\tests;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/flc';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);


include_once BASEPATH.'/flcAutoloader.php';


use flc\database\driver\flcDriver;
use flc\database\driver\mysql\flcMysqlDriver;
use flc\database\driver\mssql\flcMssqlDriver;
use flc\database\driver\postgres\flcPostgresDriver;

$driver_id = 'postgress';

flcDriver::$dblog_console = true;


if ($driver_id == 'mysql' ) {
    $driver = new flcMysqlDriver();
    $driver->initialize(null, '192.168.18.51', 3306, 'db_tests', 'root', '202106');
} else if ($driver_id == 'mssql') {
    $driver = new flcMssqlDriver();
    $driver->initialize(null, '192.168.18.49', 1433, 'db_tests', 'sa', '202106');
} else {
    $driver = new flcPostgresDriver();
    $driver->initialize(null, '192.168.18.51', 5432, 'db_tests', 'postgres', '202106');
}

$driver->open();

print_r($driver->column_data('tb_column_defs'));


foreach (array('latin1', 'utf8') as $charset) {

    // Set character set, to show its impact on some values (e.g., length in bytes)
    $driver->set_charset($charset);

    $query = "SELECT * from tb_column_defs";

    echo "======================\n";
    echo "Character Set: $charset\n";
    echo "======================\n";

    if ($result = $driver->execute_query($query)) {

        /* Get field information for all columns */
        $finfo = $result->field_data();

        foreach ($finfo as $val) {
            printf("Name:     %s\n",   $val->col_name);
            printf("type:     %s\n",   $val->col_type);
            printf("Length:   %d\n",   $val->col_length);
            printf("scale:    %d\n\n", $val->col_scale);
            printf("nullable: %d\n\n", $val->col_is_nullable);
        }
        $result->free_result();
    }


    echo "======================\n";
    echo "Primary keys\n";
    echo "======================\n";
    //print_r($driver->primary_key('tb_column_defs_2'));
    print_r($driver->primary_key('tb_column_defs'));
}

$driver->close();

