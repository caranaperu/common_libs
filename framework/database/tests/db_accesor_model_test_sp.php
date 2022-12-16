<?php

namespace framework\database\tests;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/framework';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);


include_once BASEPATH.'/flcAutoloader.php';


use framework\core\accessor\constraints\flcConstraints;
use framework\core\accessor\constraints\flcJoinEntry;
use framework\core\accessor\constraints\flcJoins;
use framework\core\accessor\flcDbAccessor;
use framework\core\entity\flcBaseEntity;
use framework\database\driver\flcDriver;
use framework\database\driver\mysql\flcMysqlDriver;

flcDriver::$dblog_console = true;

class class_entity extends flcBaseEntity {
    public function __construct() {
        $this->fields = ['id' => null, 'name' => null, 'afloat' => null, 'aboolean' => null];
        $this->key_fields = ['id'];
        $this->id_field = 'id';
        $this->table_name = 'tb_class';
        $this->field_types = ['aboolean' => 'bool', 'afloat' => 'nostring'];
    }

}


class dbFacturaAccessor extends flcDbAccessor {
    protected function get_add_query(flcBaseEntity $p_entity, ?string $p_suboperation = null): string {
        return $this->db->callable_string('insert_tb_factura_header_record', 'procedure', 'scalar', [
            $p_entity->numero,
            $p_entity->descripcion
        ]);
        //return $this->db->callable_string('insert_tb_factura_header_record_EXCEPTION', 'procedure', 'scalar', [$p_entity->numero,$p_entity->descripcion]);
    }

    protected function get_fetch_query(flcBaseEntity $p_entity, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null): string {
        return $this->db->callable_string('get_factura_header_resultset', 'procedure', 'records',[8,'Producto 1']);

    }
}


class factura_header_entity extends flcBaseEntity {
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


//$driver->set_rowversion_field('aboolean');

$factura_da = new dbFacturaAccessor($driver);
$factura_header = new factura_header_entity();
$factura_header->numero = 200;
$factura_header->descripcion = "Prueba 200";

// try to delete to allow the test (unique key,remenber)
$ret = $factura_da->delete($factura_header);
echo $ret.PHP_EOL;
$driver->trans_mark_clean(); // in case doesnt exist , its ok .


// add a header
$ret = $factura_da->add($factura_header);
echo $ret.PHP_EOL;
print_r($factura_header).PHP_EOL;

// try to update
$factura_header->descripcion = 'Producto 200-update';
//$factura_header->descripcion = null;
$ret = $factura_da->update($factura_header);
echo $ret.PHP_EOL;
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

$factura_items_db = new dbFacturaItemsAccessor($driver);
$factura_items = new factura_items_entity();
$factura_items->item_id = 100;
$factura_items->producto = 'Item 1/1';
$factura_items->cantidad = 1000;
$factura_items->factura_nro = $factura_header->numero;
//$factura_items->factura_nro = 30000;

$ret = $factura_items_db->add($factura_items);
echo $ret.PHP_EOL;

$factura_header->descripcion = 'Producto 1 - updated';
$factura_header->numero = 28;

$ret = $factura_da->update($factura_header);
echo $ret.PHP_EOL;

$factura_items->item_id = 29;
$ret = $factura_items_db->delete($factura_items);
echo $ret.PHP_EOL;


$ret = $factura_da->delete($factura_header);
echo $ret.PHP_EOL;


$c = new \flcConstraints();
$c->set_where_fields([['descripcion', 'like'], ['numero', '>']]);
$c->set_order_by_fields(['numero']);
$c->set_select_fields(['numero', 'descripcion']);
$c->set_start_row(0);
$c->set_end_row(4);

$join1 = new flcJoinEntry();
$join1->initialize('tb_factura_items', 'tb_factura_header', [
    'factura_nro' => 'numero'
], ['item_id', 'producto', 'cantidad']);
$joins = new flcJoins();
$joins->add_join($join1);

$c->set_joins($joins);

$factura_header->numero = 4;
$factura_header->descripcion = 'producto';

$factura_da->get_fetch_query($factura_header, $c);
echo PHP_EOL;

$results = $factura_da->fetch($factura_header, $c);
if (is_array($results)) {
    foreach ($results as $record) {
        print_r($record);
    }
}


$join1 = new flcJoinEntry();
$join1->initialize('table1', 'table_ref', [
    'tb1_field1' => 'tbref_field1',
    'tb1_field2' => 'tbref_field2'
], ['tb1_field3 as field3', 'tbl_field4']);


echo $join1->get_join_str().PHP_EOL;
echo $join1->get_joined_fields_str().PHP_EOL;


$joins = new flcJoins();
$joins->add_join($join1);

$join2 = new flcJoinEntry();
$join2->initialize('table2', 'table_ref_2', [
    'tb1_field1' => 'tbref_field1',
    'tb1_field2' => 'tbref_field2'
], ['tb1_field3', 'tbl_field4 as field4']);
echo $join2->get_join_str().PHP_EOL;
echo $join2->get_joined_fields_str().PHP_EOL;

$joins->add_join($join2);
echo $joins->get_join_string().PHP_EOL;
echo $joins->get_join_fields_string().PHP_EOL;

$c->set_joins($joins);
