<?php
namespace flc\test_db\tests;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/flc';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);


include_once BASEPATH.'/flcAutoloader.php';


use flc\core\accessor\flcDbAccessor;
use flc\core\dto\flcInputDataProcessor;
use flc\core\model\flcBaseModel;
use flc\database\driver\flcDriver;
use flc\database\driver\mssql\flcMssqlDriver;
use flc\database\driver\mysql\flcMysqlDriver;
use flc\database\driver\postgres\flcPostgresDriver;

flcDriver::$dblog_console = true;
global $driver_id;
$driver_id = 'mssql';

class class_entity extends flcBaseModel {
    public function __construct() {
        global $driver_id;

        if ($driver_id == 'pgsql') {
            $rowversion_field='xmin';
        } else {
            $rowversion_field='rowid';
        }

        $this->fields = ['name'=> null,'value'=>null,$rowversion_field=>null];
        $this->key_fields = ['name'];
        $this->table_name = 'tb_rowid';
        $this->field_types = [$rowversion_field=>'rowversion','value'=> 'nostring'];
        parent::__construct(null,null);
    }

}


class dbAcc extends flcDbAccessor {

}


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
$driver->set_trans_unique(true);
$driver->trans_mark_clean();

$driver->trans_begin();


$da = new dbAcc($driver);

$class = new class_entity();

$class->name ='hola';
$class->value =1;

$ret = $da->delete($class);
print_r($ret).PHP_EOL;

$ret = $da->add($class,null);
print_r($ret).PHP_EOL;
// in postgress if activatge this line we will se that the rowversion changes after update,
// otherwise within the same transaction is the same. In sql server this behaviour is different
// and allways change.

//$driver->trans_complete();


if ($ret->is_success()) {
    $class->value =2;
    $ret = $da->update($class);
    print_r($ret).PHP_EOL;

}


$driver->trans_complete();
$driver->close();
//$class->set_values(['afloat'=>1000.01,'aboolean' => false]);

