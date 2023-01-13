<?php

$system_path = '/var/www/common/framework';

// Path to the system directory
define('BASEPATH', $system_path);
include_once BASEPATH.'/flcAutoloader.php';

use framework\database\driver\mssql\flcMssqlDriver;


class clase {
    public int    $Commercial_Description;
    public string $Fecha_Llegada;
}

class modelo {
    public string $f_modelo;
    public string $f_modelotext;
}

/*
$dsn = 'postgres://arana:mipassw@localhost:8080/midb?parameter=2';

$p = parse_url($dsn);
print_r($p);

$port = parse_url($dsn, PHP_URL_PORT);
print_r('El port es : '.$port);*/


$driver = new flcMssqlDriver();

$driver->initialize(null, '192.168.18.9', 1532, 'veritrade', 'sa', 'melivane', $p_charset = 'utf8');

if ($driver->open()) {
    echo 'Host     : '.$driver->get_host().PHP_EOL;
    echo 'Port     : '.$driver->get_port().PHP_EOL;
    echo 'Database : '.$driver->get_database().PHP_EOL;
    echo 'USer     : '.$driver->get_user().PHP_EOL;

    $RES = $driver->execute_query("select * from t_modelo ");

    if ($RES && $RES->num_rows() > 0) {
        echo 'Hay registros'.PHP_EOL;
        /*  $results  = $RES->result_array();
          if ($results && count($results)) {
              echo 'Se leyeron'.PHP_EOL;
          }

          $results  = $RES->result_object();*/

        /*        print_r($RES->_get_charset_bytes_per_character(1)).PHP_EOL;
                print_r($RES->_get_charset_bytes_per_character(100)).PHP_EOL;
                print_r($RES->_get_charset_bytes_per_character(63)).PHP_EOL;
                print_r($RES->_get_charset_bytes_per_character(200)).PHP_EOL;*/

        $results = $RES->custom_result_object("clase");

        echo '**************** ROW ARRAY 1 ********************************'.PHP_EOL;
        print_r($RES->row_array(1));
        echo '**************** ROW OBJECT 2 ********************************'.PHP_EOL;
        print_r($RES->row_object(2));
        echo '**************** ROW OBJECT 1 ********************************'.PHP_EOL;
        print_r($RES->row_object(1));
        echo '**************** CUSTM ROW OBJECT 40 , clase ********************************'.PHP_EOL;
        print_r($RES->custom_row_object(40, "modelo"));
        echo '**************** CUSTM ROW OBJECT 2 , clase ********************************'.PHP_EOL;
        print_r($RES->custom_row_object(1, "modelo"));
        echo '**************** FIRST ROW ********************************'.PHP_EOL;
        print_r($RES->first_row());

        echo 'Array'.PHP_EOL;
        echo '**************** FIRST ROW ARRAY********************************'.PHP_EOL;
        print_r($RES->first_row('array'));
        echo '**************** NEXT ROW ARRAY********************************'.PHP_EOL;
        print_r($RES->next_row('array'));
        echo '**************** LAST ROW ARRAY********************************'.PHP_EOL;
        print_r($RES->last_row('array'));
        echo '**************** PREVIOS ROW ARRAY********************************'.PHP_EOL;
        print_r($RES->previous_row('array'));

        echo 'Object'.PHP_EOL;
        print_r($RES->first_row('object'));
        print_r($RES->next_row('object'));
        print_r($RES->last_row('object'));
        print_r($RES->previous_row('object'));

        echo 'Menu'.PHP_EOL;
        print_r($RES->first_row('modelo'));
        print_r($RES->next_row('modelo'));
        print_r($RES->last_row('modelo'));
        print_r($RES->previous_row('modelo'));

        /*
        foreach ($RES->result_array() as $row) {
            //Seteamos los valores de la base en el modelo.
            $results[] = $row;
            $row = null;
        }*/
    }

    echo '**************** execute query ********************************'.PHP_EOL;
    $query = $driver->execute_query("select top 10 * from veritrade");

    // print_r($query->num_rows());

    /*foreach ($query->result_array() as $row) {
        print_r($row);
    }*/

    while ($row = $query->unbuffered_row('clase')) {
        print_r($row);
    }

    print_r($driver->error());

    echo $query->num_fields().PHP_EOL;
    print_r($query->list_fields());
    echo PHP_EOL;
    print_r($query->field_data());


    echo PHP_EOL;
    print_r($driver->get_version());
    echo PHP_EOL;

    echo $driver->escape(true).PHP_EOL;
    echo $driver->escape(100).PHP_EOL;
    echo $driver->escape(null).PHP_EOL;


    echo PHP_EOL;
    echo '**************** LIST COLUMNS ********************************'.PHP_EOL;
    print_r($driver->list_columns('veritrade')).PHP_EOL;
    echo '***************** PRIMARY KEY t_modelo ********************************'.PHP_EOL;
    print_r($driver->primary_key('t_modelo'));
    echo PHP_EOL;


    echo '*****************COLUMN DATA veritrade********************************'.PHP_EOL;
    print_r($driver->column_data('veritrade')).PHP_EOL;

    echo '*****************COLUMN DATA schtest.tb_test ********************************'.PHP_EOL;
    print_r($driver->column_data('tb_test', 'schtest')).PHP_EOL; //  work because specify the schema is in another database

    echo '*****************COLUMN DATA WITH SELECT DATABASE veritrade_baterias ********************************'.PHP_EOL;
    // Works because first select database , this is not standard
    $driver->select_database('veritrade_baterias');
    print_r($driver->column_data('veritrade')).PHP_EOL; // Dont work because is in another database

    $driver->select_database('veritrade');

    echo '****************** COLUMN EXISTS : ';
    echo $driver->column_exists('f_modelo', 't_modelo').PHP_EOL;

    echo '***************** COUNT ALL ******************'.PHP_EOL;
    echo $driver->count_all('t_modelo').PHP_EOL;

    echo '****************** GET VERSION **********************'.PHP_EOL;
    print_r($driver->get_version()).PHP_EOL;

    echo '******************* GET DRIVER *******************'.PHP_EOL;
    print_r($driver->get_db_driver()).PHP_EOL;

    echo '************** LIST TABLES dbo / schtest ******************'.PHP_EOL;
    print_r($driver->list_tables('dbo', 'tb_')).PHP_EOL;
    print_r($driver->list_tables('schtest')).PHP_EOL;
    print_r($driver->list_tables('', 'veri')).PHP_EOL;


    echo '*************** TABLE EXISTS ******************'.PHP_EOL;
    echo $driver->table_exists('dbo', 'tb_marcas');
    echo PHP_EOL;
    echo $driver->table_exists('dbo', 'veritrade');
    echo PHP_EOL;

    echo '*************** TABLE EXISTS sobre veritrade_baterias ******************'.PHP_EOL;
    $driver->select_database('veritrade_baterias');
    echo $driver->table_exists('', 'veritrade_122019_out');
    echo PHP_EOL;
    print_r($driver->list_tables('')).PHP_EOL;

    echo '*************** INSERT STRING *********************'.PHP_EOL;
    echo $driver->insert_string('tb_sys_menu', [
            'field1' => 120,
            'field2' => 'Carlos',
            'field3' => false
        ]).PHP_EOL;

    echo '*************** UPDATE STRING *********************'.PHP_EOL;
    echo $driver->update_string('tb_sys_menu', [
            'field1' => 120,
            'field2' => 'Carlos',
            'field3' => false
        ], "field5 = ".$driver->escape(true).' and field_6=100').PHP_EOL;


    $driver->set_trans_unique(true);

    $driver->trans_start();
    print_r($driver->error());

    $driver->trans_start();
    print_r($driver->error());
    $driver->trans_savepoint('ssss');
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

