<?php

use framework\database\driver\postgres\flcPostgresDriver;

include_once('../../driver/flcDriver.php');
include_once('../../driver/postgres/flcPostgresDriver.php');

class menu {
    public string $sys_systemcode;
    public string $menu_codigo;
}

/*
$dsn = 'postgres://arana:mipassw@localhost:8080/midb?parameter=2';

$p = parse_url($dsn);
print_r($p);

$port = parse_url($dsn, PHP_URL_PORT);
print_r('El port es : '.$port);*/


$driver = new flcPostgresDriver();
$driver->initialize(null, '192.168.18.30', 5432, 'db_flabsregs', 'postgres', 'melivane');
if ($driver->open()) {
    echo 'Host     : '.$driver->get_host().PHP_EOL;
    echo 'Port     : '.$driver->get_port().PHP_EOL;
    echo 'Database : '.$driver->get_database().PHP_EOL;
    echo 'USer     : '.$driver->get_user().PHP_EOL;

    $RES = $driver->execute_query("select * from tb_sys_menu  order by menu_id");

    if ($RES && $RES->num_rows() > 0) {
        echo 'Hay registros'.PHP_EOL;
        /*  $results  = $RES->result_array();
          if ($results && count($results)) {
              echo 'Se leyeron'.PHP_EOL;
          }

          $results  = $RES->result_object();*/
        $results = $RES->custom_result_object("Menu");
        print_r($RES->row_array(2));
        print_r($RES->row_object(2));
        print_r($RES->custom_row_object(40, "Menu"));
        print_r($RES->custom_row_object(2, "Menu"));
        print_r($RES->first_row());

        echo 'Array'.PHP_EOL;
        print_r($RES->first_row('array'));
        print_r($RES->next_row('Menu'));
        print_r($RES->last_row('array'));
        print_r($RES->previous_row('array'));

        echo 'Object'.PHP_EOL;
        print_r($RES->first_row('object'));
        print_r($RES->next_row('Menu'));
        print_r($RES->last_row('object'));
        print_r($RES->previous_row('object'));

        echo 'Menu'.PHP_EOL;
        print_r($RES->first_row('Menu'));
        print_r($RES->next_row('Menu'));
        print_r($RES->last_row('Menu'));
        print_r($RES->previous_row('Menu'));

        /*
        foreach ($RES->result_array() as $row) {
            //Seteamos los valores de la base en el modelo.
            $results[] = $row;
            $row = null;
        }*/
    }

    $query = $driver->execute_query("select * from tb_sys_menu  order by menu_id");

    while ($row = $query->unbuffered_row()) {
        print_r($row);
    }

    echo $query->num_fields().PHP_EOL;
    echo '****************** LIST FIELDS : ';
    print_r($query->list_fields());
    echo PHP_EOL;
    echo '****************** FIELD DATA : ';
    print_r($query->field_data());


    echo PHP_EOL;
    print_r($driver->get_version());
    echo PHP_EOL;

    echo $driver->escape(true).PHP_EOL;
    echo $driver->escape(100).PHP_EOL;
    echo $driver->escape(null).PHP_EOL;

    $m = new Menu();
    $m->menu_codigo = '100';
    $m->sys_systemcode = 'flabscorp';

    print_r($driver->escape($m)).PHP_EOL;


    $a = ['xxxx',2,'yyyy',true,2.5,null];
    print_r($driver->escape($a)).PHP_EOL;;

    print_r($driver->escape_str(['Test1','%Test2%'],true)).PHP_EOL;;
    print_r($driver->escape_str("Chicago O'Hare",true)).PHP_EOL;;
    print_r($driver->escape_str(100,true)).PHP_EOL;;


    print_r($driver->list_columns('tb_sys_menu')).PHP_EOL;
    print_r($driver->primary_key('tb_sys_menu')).PHP_EOL;

    echo PHP_EOL;
    print_r($driver->escape_identifiers("select * from db_labsregs.public.tb_sys_menu where field1 = 'carlos\n'"));
    echo PHP_EOL;
    print_r($driver->escape_identifiers(["db_labsregs.public.tb_sys_menu","tb_sys menu"]));
    echo PHP_EOL;
    print_r($driver->escape_identifiers("O'Reilly"));
    echo PHP_EOL;
    print_r($driver->escape_identifiers(100));
    echo PHP_EOL;
    print_r($driver->escape_identifiers(100.66));
    echo PHP_EOL;
    print_r($driver->escape_identifiers("public.std.table_name"));
    echo PHP_EOL;



    echo '*****************COLUMN DATA********************************'.PHP_EOL;
    print_r($driver->column_data('tb_sys_menu')).PHP_EOL;
    print_r($driver->column_data('routines','information_schema')).PHP_EOL; //  work because specify the schema is in another database


    echo '****************** COLUMN EXISTS : ';
    echo $driver->column_exists('menu_descripcion','tb_sys_menu').PHP_EOL;

    echo '*************************************************'.PHP_EOL;
    echo $driver->count_all('tb_sys_menu').PHP_EOL;

    echo '*************************************************'.PHP_EOL;
    print_r($driver->get_version()).PHP_EOL;

    echo '*************************************************'.PHP_EOL;
    print_r($driver->get_db_driver()).PHP_EOL;

    echo '*************************************************'.PHP_EOL;
    print_r($driver->list_tables('public','tb_sys')).PHP_EOL;
    echo '*************************************************'.PHP_EOL;
    print_r($driver->list_tables('information_schema')).PHP_EOL;

    echo '*************************************************'.PHP_EOL;
    echo $driver->table_exists('public','tb_sys_menu').PHP_EOL;

    echo '*************************************************'.PHP_EOL;
    echo $driver->insert_string('tb_sys_menu',['field1'=>120,'field2'=>'Carlos','field3'=>false]).PHP_EOL;

    echo '*************************************************'.PHP_EOL;
    echo $driver->update_string('tb_sys_menu',['field1'=>120,'field2'=>'Carlos','field3'=>false],"field5 = ".$driver->escape(true).' and field_6=100',10).PHP_EOL;


    $a =[];
    $a[0][] = 'test 01-1';
    $a[0][] = 'test 01-2';
    $a[1][] = 'test 02-1';
    $a[1][] = 'test 02-2';

    print_r($a).PHP_EOL;
    echo 'Existe test 02-1 in 1:'.in_array('test 02-1',$a[1]).PHP_EOL;
    echo 'Existe test 01-1 in 1:'.in_array('test 01-1',$a[1]).PHP_EOL;
    echo 'Existe test 01-1 in 0:'.in_array('test 01-1',$a[0]).PHP_EOL;

    echo PHP_EOL;

    echo 'Posicion test 02-2 in 1:'.array_search('test 02-2',$a[1]).PHP_EOL;
    echo 'Posicion test 01-2 in 1:'.array_search('test 01-2',$a[1]).PHP_EOL;
    echo 'Posicion test 01-1 in 0:'.array_search('test 01-1',$a[0]).PHP_EOL;


    $driver->set_trans_unique(true);

    $driver->trans_start();
    print_r($driver->error());

    $driver->trans_start();
    print_r($driver->error());
    $driver->trans_savepoint('ssss');
    print_r($driver->error());
    $driver->trans_rollback('ssss');
    print_r($driver->error());
    $driver->trans_remove_savepoint('ssss');
    print_r($driver->error());
    $driver->trans_commit();
    print_r($driver->error());
    $driver->trans_remove_savepoint('xxxx');
    print_r($driver->error());
    $driver->trans_complete();
    print_r($driver->error());

    $driver->close();



} else {
    echo "Fallo la coneccion";
}

