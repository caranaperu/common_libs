<?php

use framework\database\driver\mysql\flcMysqlConnection;

include_once('./flcConnection.php');
include_once('./driver/flcDriver.php');

include_once('./driver/mysql/flcMysqlConnection.php');
include_once('./driver/mysql/flcMysqlDriver.php');



class clase {
    public int $id;
    public string $name;
}

/*
$dsn = 'postgres://arana:mipassw@localhost:8080/midb?parameter=2';

$p = parse_url($dsn);
print_r($p);

$port = parse_url($dsn, PHP_URL_PORT);
print_r('El port es : '.$port);*/


$con = new flcMysqlConnection();
$con->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

if ($con->open()) {
    echo 'Host     : '.$con->get_host().PHP_EOL;
    echo 'Port     : '.$con->get_port().PHP_EOL;
    echo 'Database : '.$con->get_database().PHP_EOL;
    echo 'USer     : '.$con->get_user().PHP_EOL;

    $driver = new \framework\database\driver\mysql\flcMysqlDriver($con);
    $RES = $driver->execute_query("select * from tb_class ");

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
        print_r($RES->custom_row_object(40, "clase"));
        echo '**************** CUSTM ROW OBJECT 2 , clase ********************************'.PHP_EOL;
        print_r($RES->custom_row_object(1, "clase"));
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
        print_r($RES->first_row('clase'));
        print_r($RES->next_row('clase'));
        print_r($RES->last_row('clase'));
        print_r($RES->previous_row('clase'));

        /*
        foreach ($RES->result_array() as $row) {
            //Seteamos los valores de la base en el modelo.
            $results[] = $row;
            $row = null;
        }*/
    }

    $query = $driver->execute_query("select * from tb_class");
    print_r($query->num_rows());
    while ($row = $query->unbuffered_row()) {
        print_r($row);
    }

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

    $m = new clase();
    $m->id = 100;
    $m->name = 'flabscorp';

    print_r($driver->escape($m)).PHP_EOL;

    $a = ['xxxx',2,'yyyy',true,2.5,null];
    print_r($driver->escape($a)).PHP_EOL;;
    print_r($driver->escape_str("Chicago O'Hare",true)).PHP_EOL;;
    print_r($driver->escape_str(100,true)).PHP_EOL;;


    echo PHP_EOL;
    echo '**************** LIST COLUMNS ********************************'.PHP_EOL;
    print_r($driver->list_columns('tb_class')).PHP_EOL;
    echo '***************** PRIMARY KEY ********************************'.PHP_EOL;
    print_r($driver->primary_key('tb_class'));
    echo PHP_EOL;


    echo '*****************COLUMN DATA tb_class********************************'.PHP_EOL;
    print_r($driver->column_data('tb_class')).PHP_EOL;

    echo '*****************COLUMN DATA information_schema.ROUTINES ********************************'.PHP_EOL;
    print_r($driver->column_data('ROUTINES','information_schema')).PHP_EOL; //  work because specify the schema is in another database

    echo '*****************COLUMN DATA WITH SELECT DATABASE ********************************'.PHP_EOL;
    // Works because first select database , this is not standard
    $driver->select_database('INFORMATION_SCHEMA');
    print_r($driver->column_data('ROUTINES')).PHP_EOL; // Dont work because is in another database

    echo '****************** COLUMN EXISTS : ';
    echo $driver->column_exists('id','tb_class').PHP_EOL;

    echo '***************** COUNT ALL ******************'.PHP_EOL;
    echo $driver->count_all('tb_class').PHP_EOL;

    echo '****************** GET VERSION **********************'.PHP_EOL;
    print_r($driver->get_version()).PHP_EOL;

    echo '******************* GET DRIVER *******************'.PHP_EOL;
    print_r($driver->get_db_driver()).PHP_EOL;

    echo '************** LIST TABLES db_tests ******************'.PHP_EOL;
    print_r($driver->list_tables('db_tests','tb_')).PHP_EOL;
    print_r($driver->list_tables('db_tests')).PHP_EOL;
    print_r($driver->list_tables('')).PHP_EOL;
    echo '************** LIST TABLES information schema ******************'.PHP_EOL;
    print_r($driver->list_tables('information_schema')).PHP_EOL;

    echo '*************** TABLE EXISTS ******************'.PHP_EOL;
    echo $driver->table_exists('db_tests','tb_class');
    echo PHP_EOL;
    echo $driver->table_exists('db_tests','tb_class2');
    echo PHP_EOL;

    echo '*************** INSERT STRING *********************'.PHP_EOL;
    echo $driver->insert_string('tb_sys_menu',['field1'=>120,'field2'=>'Carlos','field3'=>false]).PHP_EOL;

    echo '*************** UPDATE STRING *********************'.PHP_EOL;
    echo $driver->update_string('tb_sys_menu',['field1'=>120,'field2'=>'Carlos','field3'=>false],"field5 = ".$driver->escape(true).' and field_6=100',10).PHP_EOL;





    $driver->trans_start();
    print_r($driver->error());

    $driver->trans_start();
    print_r($driver->error());
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

    $con->close();


} else {
    echo "Fallo la coneccion";
}

