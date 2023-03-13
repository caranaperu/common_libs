<?php


namespace flc\test_db\tests;

//$database = 'mysql';
$database = 'sqlserver';
//$database = 'postgres';


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
use flc\core\dto\flcInputDataProcessor;
use flc\core\model\flcBaseModel;
use flc\database\driver\flcDriver;
use flc\database\driver\mssql\flcMssqlDriver;
use flc\database\driver\mysql\flcMysqlDriver;
use flc\database\driver\postgres\flcPostgresDriver;

class flcHeaderInputDataProcessorFetch extends flcInputDataProcessor {
    public function process_input_data(flcBaseModel $p_model) {
        // simulated parser
        $this->operation = 'fetch';
        $this->fields = ['numero' => 200, 'descripcion' => '00'];
        $this->filter_fields = ['numero' => '>', 'descripcion' => 'like'];
        $this->sort_fields = ['descripcion' => 'desc','numero'];
    }

}

class flcHeaderInputDataProcessorAdd extends flcInputDataProcessor {

    public function process_input_data(flcBaseModel $p_model) {
        // simulated parser
        $this->operation = 'add';
        $this->fields = ['numero' => 500, 'descripcion' => '300', 'customer_id' => 11];
    }
}

class flcHeaderInputDataProcessorUpdate extends flcInputDataProcessor {

    public function process_input_data(flcBaseModel $p_model) {
        // simulated parser
        $this->operation = 'update';
        $this->fields = ['numero' => 500, 'descripcion' => 'ahora soy 600', 'customer_id' => 12];
        $this->filter_fields = ['numero' => 300, 'descripcion' => 'ahora soy 600', 'customer_id' => 12];
    }
}

class flcHeaderInputDataProcessorDelete extends flcInputDataProcessor {

    public function process_input_data(flcBaseModel $p_model) {
        // simulated parser
        $this->operation = 'delete';
        $this->fields = ['numero' => 500];
        $this->filter_fields = ['numero' => '>='];

    }
}

class flcHeaderInputDataProcessorRead extends flcInputDataProcessor {

    public function process_input_data(flcBaseModel $p_model) {
        // simulated parser
        $this->operation = 'read';
        $this->fields = ['numero' => 200];

    }
}


flcDriver::$dblog_console = true;


if ($database == 'mysql') {
    $driver = new flcMysqlDriver();
    $driver->initialize(null, '192.168.18.51', 3306, 'db_tests', 'root', '202106');
} else {
    if ($database == 'sqlserver') {
        $driver = new flcMssqlDriver();
        $driver->initialize(null, '192.168.18.49', 1433, 'db_tests', 'sa', '202106');

    } else {
        $driver = new flcPostgresDriver();
        $driver->initialize(null, '192.168.18.51', 5432, 'db_tests', 'postgres', '202106');

    }
}

if (!$driver->open()) {
    exit('not connected');
}

$driver->set_trans_unique(true);
$driver->trans_mark_clean();

$driver->trans_begin();

class customer_model extends flcBaseModel {
    public function __construct(?flcInputDataProcessor $p_input_data) {
        $this->fields = ['id' => null, 'name' => null];
        $this->key_fields = ['id'];
        $this->table_name = 'tb_customer';
        $this->field_types = ['id' => 'nostring'];

        parent::__construct($p_input_data);

    }

}

class factura_items_entity extends flcBaseModel {
    public function __construct(?flcDriver $p_driver, ?flcInputDataProcessor $p_input_data) {
        $this->fields = ['item_id' => null, 'producto' => null, 'cantidad' => null, 'factura_nro' => null];
        $this->key_fields = ['item_id'];
        //$this->id_field = 'item_id';
        $this->table_name = 'tb_factura_items';
        $this->field_types = ['item_id' => 'nostring', 'factura_nro' => 'nostring'];

        parent::__construct($p_driver, $p_input_data);

    }

}

class factura_header_entity extends flcBaseModel {
    public function __construct(?flcDriver $p_driver, ?flcInputDataProcessor $p_input_data) {
        $this->fields = ['numero' => null, 'descripcion' => null, 'customer_id' => null];
        $this->fields_ro = ['name' => null];

        $this->key_fields = ['numero'];
        $this->table_name = 'tb_factura_header';
        $this->field_types = ['numero' => 'nostring','customer_id' => 'nostring'];
        $this->fields_operations = ['name'=> 'f'];

        $this->accessor = new flcDbAccessor($p_driver);

        parent::__construct($p_driver, $p_input_data);

    }

    public function is_valid_field($field, $value): bool {
        if ($field === 'numero') {
            return $value < 1000;

        }

        return true;
    }

    public function &get_delete_constraints(?string $p_suboperation = null): ?flcConstraints {
        $c = $this->input_data->get_constraints('delete');

        return $c;
    }

    public function &get_fetch_constraints(?string $p_suboperation = null): ?flcConstraints {


        if ($p_suboperation == 'fetchjoined') {
            $c = $this->input_data->get_constraints('fetch');
            // do a joined fetch factura/items
            // using full constraints and join
            $c->set_select_fields(['numero', 'descripcion']);

            $factura_items = new factura_items_entity(null,null);

            /*
                select numero, descripcion, tb_factura_items.item_id, tb_factura_items.producto, tb_factura_items.cantidad
                from tb_factura_header
                    INNER JOIN tb_factura_items on tb_factura_items.factura_nro = tb_factura_header.numero
                where numero > 100
                order by numero
                offset 0 rows fetch next 5 rows only
             */
            $join1 = new flcJoinEntry();
            $join1->initialize($factura_items, $this, [
                'factura_nro' => 'numero'
            ], ['item_id', 'producto', 'cantidad']);
            $joins = new flcJoins();
            $joins->add_join($join1);

            $c->set_joins($joins);

        } else {
            $c = $this->input_data->get_constraints('fetch');
        }

        return $c;
    }

    public function &get_read_constraints(?string $p_suboperation = null): ?flcConstraints {
        if ($p_suboperation == 'readjoined') {
            $c = new flcConstraints();
            // do a joined fetch factura/items
            // using full constraints and join
            $c->set_select_fields(['numero', 'descripcion', 'customer_id']);

            $cmodel = new customer_model(null,null);

            $j = new flcJoinEntry();
            $j->initialize($cmodel, $this, ['id' => 'customer_id'], ['name'], flcJoinEntry::$LEFT_JOIN);

            $joins = new flcJoins();
            $joins->add_join($j);

            $c->set_joins($joins);
        } else {
            return $this->input_data->get_constraints('read');
        }

        return $c;
    }

}


$id = new flcHeaderInputDataProcessorFetch([]);
$factura_header = new factura_header_entity($driver, $id);

// do a fetch
echo 'Resultados primer fetch'.PHP_EOL;
$results = $factura_header->fetch();
if ($results->is_success()) {
    foreach ($results->get_result_array() as $record) {
        print_r($record);
    }
}

// do a fetch with suboperation
$results = $factura_header->fetch('fetchjoined');
if ($results->is_success()) {
    foreach ($results->get_result_array() as $record) {
        print_r($record);
    }
}


// do an add
$id = new flcHeaderInputDataProcessorAdd([]);
$factura_header = new factura_header_entity($driver, $id);

$ret = $factura_header->add();
print_r($ret).PHP_EOL;

print_r($factura_header->get_fields());

// do an update
$id = new flcHeaderInputDataProcessorUpdate([]);
$factura_header = new factura_header_entity($driver, $id);

$ret = $factura_header->update();
print_r($ret).PHP_EOL;


// do a read
$id = new flcHeaderInputDataProcessorRead([]);
$factura_header = new factura_header_entity($driver, $id);

$ret = $factura_header->read();
print_r($ret).PHP_EOL;

print_r($factura_header->get_fields());

$ret = $factura_header->read('readjoined');
print_r($ret).PHP_EOL;

print_r($factura_header->get_fields());

// delete
$id = new flcHeaderInputDataProcessorDelete([]);
$factura_header = new factura_header_entity($driver, $id);

$ret = $factura_header->delete();
print_r($ret).PHP_EOL;

$driver->trans_complete();
$driver->close();
exit();
