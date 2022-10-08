<?php

use framework\database\driver\mssql\flcMssqlDriver;

include_once('../../driver/flcDriver.php');
include_once('../../driver/mssql/flcMssqlDriver.php');

//ini_set('memory_limit','256M'); // This also needs to be increased in some cases. Can be changed to a higher value as per need)
ini_set('sqlsrv.ClientBufferMaxKBSize', '200000'); // Setting to 512M
$driver = new flcMssqlDriver();
$driver->initialize(null, '192.168.18.9', 1532, 'veritrade', 'sa', 'melivane', $p_charset = 'utf8');


function print_resultsets($driver, $query) {
    if ($query) {
        $numresults = $query->get_num_resultsets();

        for ($i = 0; $i < $numresults; $i++) {
            $res = $query->get_resultset_result($i);

            if ($res) {
                echo PHP_EOL.'  --- Resultset -----'.PHP_EOL;

                if ($res->num_rows() > 0) {
                    foreach ($res->result_array() as $row) {
                        print_r($row);
                    }
                    $res->free_result();;
                }

                // PONER COMO HACER FREEEEEEEE
            } else {
                $errors = $driver->error();
                if ($errors) {
                    print_r($errors);
                }
            }

        }

        echo PHP_EOL.'  --- Output parameters -----'.PHP_EOL;

        $outparams = $query->get_out_params();
        if (isset($outparams)) {
            echo PHP_EOL;
            $paramtoshow = $outparams->get_out_params();
            foreach ($paramtoshow as $key => $value) {
                echo 'EL RESULTAOD : '.$key.' - '.$value.PHP_EOL;
            }


        }
    } else {
        print_r($driver->error());
    }

}

if ($driver->open()) {
    $driver = new flcMssqlDriver();

    $updstr = $driver->update_string('schtest.tb_test', [
        'char_col' => "'Soy charcol'", 'money_col' => 35.10
    ], "{$driver->cast_param($driver->escape_identifiers('char_col'),'string',null)} like '%charcol%'", 10);
    echo $updstr.PHP_EOL;

    $insstr = $driver->insert_string('schtest.tb_test',['id'=>100,'char_col'=>'Soy charcoal 35','money_col'=>12.88]);
    echo $insstr.PHP_EOL;


    $driver->close();
}