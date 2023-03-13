<?php


namespace flc\test_db\tests;
$database = 'mysql';
//$database = 'sqlserver';
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
use flc\core\model\flcBaseModel;
use flc\database\driver\flcDriver;
use flc\database\driver\mssql\flcMssqlDriver;
use flc\database\driver\mysql\flcMysqlDriver;
use flc\database\driver\postgres\flcPostgresDriver;

flcDriver::$dblog_console = true;

class factura_header_entity extends flcBaseModel {
    public function __construct() {
        $this->fields = ['numero' => null, 'descripcion' => null];
        //$this->key_fields = ['numero', 'descripcion'];
        $this->key_fields = ['numero'];
        //$this->id_field = 'numero';
        $this->table_name = 'tb_factura_header';
        $this->field_types = ['numero' => 'nostring'];

    }

    public function is_valid_field($field,$value) : bool {
        if ($field === 'numero') {
            return $value < 1000;

        }
        return true;
    }

}

class factura_items_entity extends flcBaseModel {
    public function __construct() {
        $this->fields = ['item_id' => null, 'producto' => null, 'cantidad' => null, 'factura_nro' => null];
        $this->key_fields = ['item_id'];
        //$this->id_field = 'item_id';
        $this->table_name = 'tb_factura_items';
        $this->field_types = ['item_id' => 'nostring', 'factura_nro' => 'nostring'];
    }

}


class dbFacturaAccessor extends flcDbAccessor {


}

class dbFacturaItemsAccessor extends flcDbAccessor {

}


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


$factura_da = new dbFacturaAccessor($driver);
$factura_items_db = new dbFacturaItemsAccessor($driver);
$factura_header = new factura_header_entity();
$factura_items = new factura_items_entity();

echo '*********** Add Factura 100 ***************'.PHP_EOL;
add_factura($factura_da, $factura_items_db, 100);


$factura_header->descripcion = 'Producto 1 - updated';
$factura_header->numero = 100;

echo '*********** Update Factura 100 ***************'.PHP_EOL;
$ret = $factura_da->update($factura_header);
print_r( $ret).PHP_EOL;


// do a fetch
$c = new flcConstraints();
$c->set_where_fields([['numero', '=']]);

echo '*********** Fetch Factura 100 ***************'.PHP_EOL;

$results = $factura_da->fetch($factura_header, $c);
print_r($results).PHP_EOL;

;

echo '*********** Fetch Factura Items 100 ***************'.PHP_EOL;
// fetch childs of factura
$factura_items->factura_nro = 100;
$c->set_where_fields([['factura_nro', '=']]);

$results = $factura_items_db->fetch($factura_items,$c);
print_r($results).PHP_EOL;

// verificar delete por referencia (factura_numero)

echo '*********** Delete Factura No existente ***************'.PHP_EOL;

// intento borrar uno no existente
$factura_items->item_id = -1;
$ret = $factura_items_db->delete($factura_items);
if (!$ret->is_success()) {
    // debedevolver el error 1002 pero queremos continuar
    // test en mssql server podria fallar.
    $driver->trans_mark_clean();
}
print_r( $ret).PHP_EOL;


$ret = delete_factura_alt($factura_da, $factura_items_db, 100);
if (!$ret->is_success()) {
    // debedevolver el error 1002 pero queremos continuar
    // test en mssql server podria fallar.
    $driver->trans_mark_clean();
}


/*****************************************************************
 * Test of constraints
 */

// first delete old records
// delete header
$ret = delete_factura($factura_da, $factura_items_db, 200);
if (!$ret->is_success()) {
    // debedevolver el error 1002 pero queremos continuar
    // test en mssql server podria fallar.
    $driver->trans_mark_clean();
}
print_r($ret).PHP_EOL;

$ret = delete_factura($factura_da, $factura_items_db, 300);
if (!$ret->is_success()) {
    print_r($ret).PHP_EOL;
    // debedevolver el error 1002 pero queremos continuar
    // test en mssql server podria fallar.
    $driver->trans_mark_clean();
}

// add some facturas
// numero = 200
add_factura($factura_da, $factura_items_db, 200);

// second facturas
// numero = 300
add_factura($factura_da, $factura_items_db, 300);


// do a fetch
//$factura_items->factura_nro = 100;
$factura_header->numero = 100;
$c->reset();
$c->set_where_fields([['numero', '>']]);
$c->set_order_by_fields(['numero']);
$c->set_select_fields(['numero', 'descripcion']);
$c->set_start_row(0);
$c->set_end_row(5);

$lfields = $factura_items->get_fields();
print_r($lfields);
if (!in_array('factura_nro',$lfields)) {
    echo 'no esta'.PHP_EOL;
} else {
    echo 'esta'.PHP_EOL;
}

$join1 = new flcJoinEntry();
$join1->initialize($factura_items, $factura_header, [
    'factura_nro' => 'numero'
], ['item_id', 'producto', 'cantidad']);
$joins = new flcJoins();
$joins->add_join($join1);

$c->set_joins($joins);


$results = $factura_da->fetch($factura_header, $c);
echo PHP_EOL;
print_r($results).PHP_EOL;


/*****************************************************************
 * Test de constraints complejos
 *
 * El resultado sera :
 *
 *  select item_id,producto,cantidad,factura_nro,tb_factura_header.numero,tb_factura_header.descripcion
 *  from tb_factura_items
 *  INNER JOIN tb_factura_header on tb_factura_header.numero = tb_factura_items.factura_nro
 *  where factura_nro=200 order by item_id
 */

$c->reset();

$factura_items->factura_nro = 200;
$c->set_where_fields([['factura_nro', '=']]);
$c->set_select_fields(['item_id', 'producto', 'cantidad', 'factura_nro']);

$join1 = new flcJoinEntry();
$join1->initialize($factura_header, $factura_items, [
    'numero' => 'factura_nro'
], ['numero', 'descripcion']);
$joins = new flcJoins();
$joins->add_join($join1);

$c->set_joins($joins);

$results = $factura_items_db->fetch($factura_items, $c);
print_r($results).PHP_EOL;


/*****************************************************************
 * Test de constraints complejos, con where de multiples tablas
 *
 * El resultado sera :
 *
 * select item_id,producto,cantidad,factura_nro,tb_factura_header.numero,tb_factura_header.descripcion from
 * tb_factura_items INNER JOIN tb_factura_header on tb_factura_header.numero = tb_factura_items.factura_nro where
 * factura_nro=200 order by item_id
 */

echo '----------> Trset de constraints complejo ---------------'.PHP_EOL;
$c->reset();

$factura_items->factura_nro = 300;
$factura_header->descripcion = 'prod';
$c->set_where_fields([['factura_nro', '='], ['tb_factura_header.descripcion', 'ilike']]);
$c->set_select_fields(['item_id', 'producto', 'cantidad', 'factura_nro']);

$join1 = new flcJoinEntry();
$join1->initialize($factura_header, $factura_items, [
    'numero' => 'factura_nro'
], ['numero', 'descripcion']);
$joins = new flcJoins();
$joins->add_join($join1);

$c->set_joins($joins);

//  select
//              item_id,producto,cantidad,factura_nro,tb_factura_header.numero,tb_factura_header.descripcion
//  from tb_factura_items
//  INNER JOIN tb_factura_header on tb_factura_header.numero = tb_factura_items.factura_nro
//  where factura_nro=300 and lower(tb_factura_header.descripcion) like '%%'
//  order by item_id

$results = $factura_items_db->fetch($factura_items, $c);
print_r($results).PHP_EOL;

$c = new flcConstraints();
$c->set_where_fields([['factura_nro', '='],['producto', '=','testtttt']]);
$factura_items = new factura_items_entity();
$factura_items->factura_nro = 200;

$results =   $factura_items_db->get_delete_query_full($factura_items,$c);
print_r($results).PHP_EOL;

$driver->trans_complete();
$driver->close();

exit();

/**
 * This delete looping using standard delete method of the accesor for the items.
 *
 * @param dbFacturaAccessor      $fa
 * @param dbFacturaItemsAccessor $fi
 * @param int                    $factura_nro
 *
 * @return flcPersistenceAccessorAnswer
 */
function delete_factura(dbFacturaAccessor $fa, dbFacturaItemsAccessor $fi, int $factura_nro) : flcPersistenceAccessorAnswer{
    echo '  ***************** Eliminando factura numero = '.$factura_nro.PHP_EOL;

    $fh = new factura_header_entity();
    $fh->numero = $factura_nro;

    $ret = $fa->delete($fh);
    if (!$ret->is_success()) {
        return $ret;
    }
    print_r( $ret).PHP_EOL;

    // now delete the left behind items associated to the header
    $c = new flcConstraints();
    $c->set_where_fields([['factura_nro', '=']]);
    $factura_items = new factura_items_entity();
    $factura_items->factura_nro = $fh->numero;

    $results = $fi->fetch($factura_items, $c);
    if ($results->is_success()) {
        $rows = $results->get_result_array();
        foreach ($rows as $record) {
            print_r($record);
            $factura_items->item_id = $record['item_id'];
            $ret = $fi->delete($factura_items);
            print_r( $ret).PHP_EOL;
        }
    }

    return $ret;
}

/**
 * This delete all items using delete_full, this another way will be used in a model , because a model
 * can contain multiples entities.
 *
 * @param dbFacturaAccessor      $fa
 * @param dbFacturaItemsAccessor $fi
 * @param int                    $factura_nro
 *
 * @return flcPersistenceAccessorAnswer
 */
function delete_factura_alt(dbFacturaAccessor $fa, dbFacturaItemsAccessor $fi, int $factura_nro) : flcPersistenceAccessorAnswer{
    echo '  ***************** Eliminando factura numero = '.$factura_nro.PHP_EOL;

    $fh = new factura_header_entity();
    $fh->numero = $factura_nro;

    $ret = $fa->delete($fh);
    if (!$ret->is_success()) {
        return $ret;
    }
    print_r($ret).PHP_EOL;

    // now delete the left behind items associated to the header
    $c = new flcConstraints();
    $c->set_where_fields([['factura_nro', '=',$fh->numero]]);
    //  not required any value in the model because the where fields contain the value to be used.
    $factura_items = new factura_items_entity();

    $ret =   $fi->delete_full($factura_items,$c);
    print_r( $ret).PHP_EOL;

    return $ret;
}

function add_factura(dbFacturaAccessor $fa, dbFacturaItemsAccessor $fi, int $factura_nro): flcPersistenceAccessorAnswer {
    echo '  ***************** Agregando factura numero = '.$factura_nro.PHP_EOL;

    $factura_header = new factura_header_entity();
    $factura_header->numero = $factura_nro;
    $factura_header->descripcion = 'Producto '.$factura_nro;

    $ret = $fa->add($factura_header);
    print_r( $ret).PHP_EOL;

    if ($ret->is_success()) {
        $factura_items = new factura_items_entity();
        $factura_items->factura_nro = $factura_nro;

        for ($i = 0; $i < 4; $i++) {
            $factura_items->item_id = $i + $factura_nro * 1000;
            $factura_items->producto = 'Item '.($i + 1).' - '.($i + $factura_nro * 1000);
            $factura_items->cantidad = 500 + ($i + $factura_nro * 1000);
            $ret = $fi->add($factura_items);
            print_r( $ret).PHP_EOL;
        }

    }
    echo '  ***************** Fin factura numero = '.$factura_nro.PHP_EOL;

    return $ret;
}
