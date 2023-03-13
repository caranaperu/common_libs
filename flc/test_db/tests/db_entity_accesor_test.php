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
use flc\core\accessor\flcPersistenceAccessorAnswer;
use flc\core\dto\flcInputDataProcessor;
use flc\core\model\flcBaseModel;
use flc\database\driver\flcDriver;
use flc\database\driver\mssql\flcMssqlDriver;
use flc\database\driver\mysql\flcMysqlDriver;
use flc\database\driver\postgres\flcPostgresDriver;

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


class factura_header_entity extends flcBaseModel {
    public function __construct(?flcDriver $p_driver, ?flcInputDataProcessor $p_input_data) {
        $this->fields = ['numero' => null, 'descripcion' => null, 'customer_id' => null];
        $this->fields_ro = ['name' => null];

        $this->key_fields = ['numero'];
        $this->table_name = 'tb_factura_header';
        $this->field_types = ['numero' => 'nostring'];

        if ($p_driver) {
            $this->accessor = new flcDbAccessor($p_driver);

        }

        parent::__construct($p_driver, $p_input_data);

    }

    public function is_valid_field($field, $value): bool {
        if ($field === 'numero') {
            return $value < 1000;

        }

        return true;
    }

    public function &get_fetch_constraints(?string $p_suboperation = null): ?flcConstraints {

        $c = null;

        if ($p_suboperation == 'fetchjoined') {
            $c = new flcConstraints();
            // do a joined fetch factura/items
            // using full constraints and join
            $c->set_where_fields([['numero', '>']]);
            $c->set_order_by_fields(['numero']);
            $c->set_select_fields(['numero', 'descripcion']);
            $c->set_start_row(0);
            $c->set_end_row(5);

            /*
                select numero, descripcion, tb_factura_items.item_id, tb_factura_items.producto, tb_factura_items.cantidad
                from tb_factura_header
                    INNER JOIN tb_factura_items on tb_factura_items.factura_nro = tb_factura_header.numero
                where numero > 100
                order by numero
                offset 0 rows fetch next 5 rows only
             */
            $factitems = new factura_items_entity(null, null);
            $factitems->factura_nro = 200;

            $join1 = new flcJoinEntry();
            $join1->initialize($factitems, $this, [
                'factura_nro' => 'numero'
            ], ['item_id', 'producto', 'cantidad']);
            $joins = new flcJoins();
            $joins->add_join($join1);

            $c->set_joins($joins);

        }

        return $c;
    }

    public function &get_read_constraints(?string $p_suboperation = null): ?flcConstraints {
        $c = null;
        if ($p_suboperation == 'readjoined') {
            $c = new flcConstraints();
            // do a joined fetch factura/items
            // using full constraints and join
            $c->set_where_fields([['numero', '=']]);
            $c->set_select_fields(['numero', 'descripcion', 'customer_id']);


            $custmodel = new customer_model(null, null);
            $custmodel->id = $this->customer_id;

            $j = new flcJoinEntry();
            $j->initialize($custmodel, $this, ['id' => 'customer_id'], ['name']);

            $joins = new flcJoins();
            $joins->add_join($j);

            $c->set_joins($joins);
        }

        return $c;
    }

}


class factura_items_entity extends flcBaseModel {
    public function __construct(?flcDriver $p_driver, ?flcInputDataProcessor $p_input_data) {
        $this->fields = ['item_id' => null, 'producto' => null, 'cantidad' => null, 'factura_nro' => null];
        $this->key_fields = ['item_id'];
        //$this->id_field = 'item_id';
        $this->table_name = 'tb_factura_items';
        $this->field_types = ['item_id' => 'nostring', 'factura_nro' => 'nostring'];
        if ($p_driver) {
            $this->accessor = new flcDbAccessor($p_driver);
        }

        parent::__construct($p_driver, $p_input_data);

    }

    public function &get_fetch_constraints(?string $p_suboperation = null): flcConstraints {
        $c = null;
        if ($p_suboperation == 'itemsxfactura') {
            $c = new flcConstraints();
            $c->set_where_fields([['factura_nro', '=']]);

            return $c;

        } else {
            if ($p_suboperation == 'itemsxfactura_joined') {
                $c = new flcConstraints();
                $c->set_where_fields([['factura_nro', '=']]);
                $c->set_select_fields(['item_id', 'producto', 'cantidad', 'factura_nro']);

                /*
                    select item_id, producto, cantidad, factura_nro, tb_factura_header.numero, tb_factura_header.descripcion
                    from tb_factura_items
                        INNER JOIN tb_factura_header on tb_factura_header.numero = tb_factura_items.factura_nro
                    where factura_nro = 200
                    order by item_id
                 */
                $fh = new factura_header_entity(null,null);
                $join1 = new flcJoinEntry();
                $join1->initialize($fh, $this, [
                    'numero' => 'factura_nro'
                ], ['numero', 'descripcion']);
                $joins = new flcJoins();
                $joins->add_join($join1);

                $c->set_joins($joins);

            } else {
                if ($p_suboperation == 'itemsxfactura_complex') {
                    /*
                        select item_id, producto, cantidad, factura_nro, tb_factura_header.numero, tb_factura_header.descripcion
                        from tb_factura_items
                            INNER JOIN tb_factura_header on tb_factura_header.numero = tb_factura_items.factura_nro
                        where factura_nro = 300 and lower(tb_factura_header.descripcion) like '%prod%'
                        order by item_id
                    */
                    $c = new flcConstraints();

                    $c->set_where_fields([['factura_nro', '='], ['tb_factura_header.descripcion', 'ilike']]);
                    $c->set_select_fields(['item_id', 'producto', 'cantidad', 'factura_nro']);

                    $fh = new factura_header_entity(null,null);
                    $fh->descripcion = 'prod';

                    $join1 = new flcJoinEntry();
                    $join1->initialize($fh, $this, [
                        'numero' => 'factura_nro'
                    ], ['numero', 'descripcion']);
                    $joins = new flcJoins();
                    $joins->add_join($join1);

                    $c->set_joins($joins);
                }
            }
        }

        return $c;
    }
}


// clear the records to do the test
$ret = delete_factura($driver, 200);
if ($ret !== flcDbAccessor::$db_error_codes['DB_OPERATION_OK']) {
    // debedevolver el error 1002 pero queremos continuar
    // test en mssql server podria fallar.
    $driver->trans_mark_clean();
}


add_factura($driver, 200, 9, 'readjoined');


$factura_header = new factura_header_entity($driver, null);
$factura_header->set_values(['descripcion' => 'Producto 1 - updated', 'numero' => 100]);

$ret = $factura_header->update();
print_r($ret).PHP_EOL;


// do a fetch
$results = $factura_header->fetch();
if ($results->is_success()) {
    foreach ($results->get_result_array() as $record) {
        print_r($record);
    }
} else {
    print_r($results);
}


// fetch childs of factura 100
$factura_items = new factura_items_entity($driver, null);
$factura_items->factura_nro = 200;

$results = $factura_items->fetch('itemsxfactura');
if ($results->is_success()) {
    foreach ($results->get_result_array() as $record) {
        print_r($record);
    }
} else {
    print_r($results);
}



$factura_header->numero = 200;
$results = $factura_header->fetch('fetchjoined');
echo PHP_EOL;
if ($results->is_success()) {
    foreach ($results->get_result_array() as $record) {
        print_r($record);
    }
} else {
    print_r($results);
}



$factura_items->factura_nro = 100;
$results = $factura_items->fetch('itemsxfactura_joined');
if ($results->is_success()) {
    foreach ($results->get_result_array() as $record) {
        print_r($record);
    }
} else {
    print_r($results);
}



$factura_items->factura_nro = 300;
$results = $factura_items->fetch('itemsxfactura_complex', [$factura_header]);
if ($results->is_success()) {
    foreach ($results->get_result_array() as $record) {
        print_r($record);
    }
} else {
    print_r($results);
}




/*******************************************************************
 * tEST DE ADD Y UPDATE PARA PRUEBA DE RELECTURA STANDARD Y JOINED
 */
class customer_model extends flcBaseModel {
    public function __construct(?flcDriver $p_driver, ?flcInputDataProcessor $p_input_data) {
        $this->fields = ['id' => null, 'name' => null];
        //$this->key_fields = ['item_id'];
        $this->id_field = 'id';
        $this->table_name = 'tb_customer';
        $this->field_types = ['id' => 'nostring'];
        $this->accessor = new flcDbAccessor($p_driver);

        parent::__construct($p_driver, $p_input_data);

    }
}

$customer = new customer_model($driver,null);
$customer->name = 'un nombre';

// Lcctura standard
$ret = $customer->add();
print_r($ret).PHP_EOL;

print_r($customer->get_fields());


// Lcctura joined
$factura_header->numero = 800;
$factura_header->descripcion = 'la descripcion 800';
$factura_header->customer_id = 9;

$ret = $factura_header->add('readjoined');
print_r($ret).PHP_EOL;

print_r($factura_header->get_all_fields());


// eliminamos el ultimo ongresado
$ret = $factura_header->delete();
print_r($ret).PHP_EOL;

$driver->trans_complete();
$driver->close();

exit();

/**
 * This delete looping using standard delete method of the accesor for the items.
 *
 * @param     $driver
 * @param int $factura_nro
 *
 * @return int|mixed
 */
function delete_factura($driver, int $factura_nro) {
    echo '  ***************** Eliminando factura numero = '.$factura_nro.PHP_EOL;

    $fh = new factura_header_entity($driver, null);
    $fh->numero = $factura_nro;


    // delete the items associated to the header
    $factura_items = new factura_items_entity($driver, null);
    $factura_items->factura_nro = $fh->numero;

    $results = $factura_items->fetch('itemsxfactura');
    if ($results->is_success()) {
        foreach ($results->get_result_array() as $record) {
            print_r($record);
            $factura_items->item_id = $record['item_id'];
            $ret = $factura_items->delete();
            print_r($ret).PHP_EOL;
        }
    } else {
        return $results->get_return_code();
    }

    // delete the main header
    $ret = $fh->delete();
    if (!$ret->is_success()) {
        return $ret->get_return_code();
    }

    print_r($ret).PHP_EOL;
}


function add_factura($driver, int $factura_nro, ?int $customer_id = null, ?string $suboperation = null): int {
    echo '  ***************** Agregando factura numero = '.$factura_nro.PHP_EOL;

    $factura_header = new factura_header_entity($driver, null);
    $factura_header->numero = $factura_nro;
    $factura_header->descripcion = 'Producto '.$factura_nro;
    if ($customer_id) {
        $factura_header->customer_id = $customer_id;

    }

    $ret = $factura_header->add($suboperation);
    print_r($factura_header->get_all_fields()).PHP_EOL;
    print_r($ret).PHP_EOL;

    if ($ret->is_success()) {
        $factura_items = new factura_items_entity($driver, null);
        $factura_items->factura_nro = $factura_nro;

        for ($i = 0; $i < 4; $i++) {
            $factura_items->item_id = $i + $factura_nro * 1000;
            $factura_items->producto = 'Item '.($i + 1).' - '.($i + $factura_nro * 1000);
            $factura_items->cantidad = 500 + ($i + $factura_nro * 1000);
            $ret = $factura_items->add();
            if ($ret->is_success()) {
                print_r($ret).PHP_EOL;
            } else {
                break;

            }

        }

        echo '  ***************** Fin factura numero = '.$factura_nro.PHP_EOL;
        if (!$ret->is_success()) {
            return $ret->get_return_code();
        } else {
            return flcDbAccessor::$db_error_codes['DB_OPERATION_OK'];
        }

    } else {
        echo '  ***************** Fin factura numero = '.$factura_nro.PHP_EOL;

        return $ret->get_return_code();
    }
}
