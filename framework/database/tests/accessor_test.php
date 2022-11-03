<?php
namespace framework\database\tests;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/framework';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);


include_once BASEPATH.'/flcAutoloader.php';


use framework\core\accessor\flcDbAccessor;
use framework\core\model\flcBaseModel;
use framework\database\driver\flcDriver;
use framework\database\driver\mysql\flcMysqlDriver;

flcDriver::$dblog_console = true;

class class_model extends flcBaseModel {
    public function __construct() {
        $this->fields = ['id'=> null,'name'=>null,'afloat'=> null,'aboolean'=>null];
        $this->ids = ['id'];
        $this->table_name = 'tb_class';
        $this->field_types = ['aboolean'=> 'bool','afloat'=>'nostring'];
    }

}


class dbAcc extends \framework\core\accessor\flcDbAccessor {

    protected function _get_add_query(): string {
        // TODO: Implement _get_add_query() method.
        return "";
    }


    protected function _get_delete_query(): string {
        // TODO: Implement _get_delete_query() method.
        return "";
    }


    protected function _get_fetch_query(): string {
        // TODO: Implement _get_fetch_query() method.
        return "";
    }
}

$driver = new flcMysqlDriver();
$driver->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');
$driver->set_rowversion_field('aboolean');

$da = new dbAcc($driver);

$class = new class_model();
$class->id = 1;
$class->name ='cambio_4';

$class->set_values(['afloat'=>1000.01,'aboolean' => false]);

$class->getCopy();

$ret = $da->update($class,null,flcDbAccessor::$open_close_flags['DB_OPEN_FLAG']);
echo $ret.PHP_EOL;

//$driver->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

$class->id = 1;
$class->name ='cambio_5';
$ret = $da->update($class,null,flcDbAccessor::$open_close_flags['DB_CLOSE_FLAG']);
echo $ret.PHP_EOL;
