<?php
namespace flc\test_db\tests;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/flc';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);


include_once BASEPATH.'/flcAutoloader.php';


use flc\core\accessor\flcDbAccessor;
use flc\core\model\flcBaseModel;
use flc\database\driver\flcDriver;
use flc\database\driver\mysql\flcMysqlDriver;

flcDriver::$dblog_console = true;

class class_entity extends flcBaseModel {
    public function __construct() {
        $this->fields = ['id'=> null,'name'=>null,'afloat'=> null,'aboolean'=>null];
        $this->key_fields = ['id'];
        $this->id_field = 'id';
        $this->table_name = 'tb_class';
        $this->field_types = ['aboolean'=> 'bool','afloat'=>'nostring'];
    }

}

class class_testforeign extends flcBaseModel {
    public function __construct() {
        $this->fields = ['id'=> null,'tfield'=>null,'fkfield'=> null];
        $this->key_fields = ['id'];
       // $this->id_field = 'id';
        $this->table_name = 'tb_testforeign';
        $this->field_types = ['fkfield'=> 'nostring'];
    }

}


class dbAcc extends flcDbAccessor {

}

$driver = new flcMysqlDriver();
$driver->initialize(null, '192.168.18.51', 3306, 'db_tests', 'root', '202106');
$driver->open();
$driver->set_trans_unique(true);
$driver->trans_mark_clean();

$driver->trans_begin();


$da = new dbAcc($driver);

$class = new class_entity();
$class->id = 1;
$class->name ='cambio_4';

$class->set_values(['afloat'=>1000.01,'aboolean' => false]);

$class->get_copy();

$ret = $da->update($class,null);
print_r($ret).PHP_EOL;

//$driver->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

$class->id = 1;
$class->name ='cambio_6';
$class->aboolean = true;
$ret = $da->update($class);
print_r($ret).PHP_EOL;

echo 'do add'.PHP_EOL;
$class->id = 100000;
$ret = $da->add($class,null/*,flcDbAccessor::$open_close_flags['DB_CLOSE_FLAG']*/);
print_r($ret).PHP_EOL;


// test foreign
$class = new class_testforeign();
$class->id = 1;
$class->tfield ='test1';
$class->fkfield =100;

$ret = $da->add($class,null);
print_r($ret).PHP_EOL;
print_r($class->get_fields());

$driver->trans_complete();
$driver->close();
//$class->set_values(['afloat'=>1000.01,'aboolean' => false]);

