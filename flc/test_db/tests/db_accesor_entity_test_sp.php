<?php

namespace flc\test_db\tests;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/flc';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);


include_once BASEPATH.'/flcAutoloader.php';


use flc\core\accessor\constraints\flcConstraints;
use flc\core\accessor\constraints\flcJoinEntry;
use flc\core\accessor\constraints\flcJoins;
use flc\core\accessor\flcDbAccessor;
use flc\core\model\flcBaseModel;
use flc\database\driver\flcDriver;
use flc\database\driver\mysql\flcMysqlDriver;

flcDriver::$dblog_console = true;

class class_entity extends flcBaseModel {
    public function __construct() {
        $this->fields = ['id' => null, 'name' => null, 'afloat' => null, 'aboolean' => null];
        $this->key_fields = ['id'];
        $this->id_field = 'id';
        $this->table_name = 'tb_class';
        $this->field_types = ['aboolean' => 'bool', 'afloat' => 'nostring'];
    }

}


class dbFacturaAccessor extends flcDbAccessor {
    protected function get_add_query(flcBaseModel $p_model, ?string $p_suboperation = null): string {
        return $this->db->callable_string('insert_tb_factura_header_record', 'procedure', 'scalar', [
            $p_model->numero,
            $p_model->descripcion
        ]);
        //return $this->db->callable_string('insert_tb_factura_header_record_EXCEPTION', 'procedure', 'scalar', [$p_model->numero,$p_model->descripcion]);
    }

    protected function get_fetch_query(flcBaseModel $p_model, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null): string {
        return $this->db->callable_string('get_factura_header_resultset', 'procedure', 'records',[8,'Producto 1']);

    }
}


class factura_header_entity extends flcBaseModel {
    public function __construct() {
        $this->fields = ['numero' => null, 'descripcion' => null];
        $this->key_fields = ['numero'];
        //$this->id_field = 'numero';
        $this->table_name = 'tb_factura_header';
        $this->field_types = ['numero' => 'nostring'];
    }

}

class dbFacturaItemsAccessor extends flcDbAccessor {

}


$driver = new flcMysqlDriver();
$driver->initialize(null, '192.168.18.51', 3306, 'db_tests', 'root', '202106');
$driver->open();
$driver->set_trans_unique(true);
$driver->trans_mark_clean();

$driver->trans_begin();



$factura_da = new dbFacturaAccessor($driver);
$factura_header = new factura_header_entity();
$factura_header->numero = 200;
$factura_header->descripcion = "Prueba 200";

// try to delete to allow the test (unique key,remenber)
$ret = $factura_da->delete($factura_header);
print_r($ret).PHP_EOL;
$driver->trans_mark_clean(); // in case doesnt exist , its ok .


// add a header
$ret = $factura_da->add($factura_header);
print_r($ret).PHP_EOL;
print_r($factura_header).PHP_EOL;

// try to update
$factura_header->descripcion = 'Producto 200-update';
//$factura_header->descripcion = null;
$ret = $factura_da->update($factura_header);
print_r($ret).PHP_EOL;
print_r($factura_header).PHP_EOL;


// fetch records
$ret = $factura_da->fetch($factura_header);
if ($ret) {
    foreach ($ret as $record) {
        echo $record['numero'].' , '.$record['descripcion'].PHP_EOL;
    }
}

$driver->trans_complete();
$driver->close();


exit();
