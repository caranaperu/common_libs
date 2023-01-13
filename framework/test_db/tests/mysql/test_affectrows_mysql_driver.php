<?php
$system_path = '/var/www/common/framework';

// Path to the system directory
define('BASEPATH', $system_path);


include_once BASEPATH.'/flcAutoloader.php';

use framework\core\accessor\core\model\database\driver\mysql\flcMysqlDriver;




$driver = new flcMysqlDriver();
$driver->initialize(null, 'localhost', 3306, 'db_tests', 'root', 'melivane');

if ($driver->open()) {

    $updstr = $driver->update_string('tb_page', [
        'value' => 'Soy charcol', 'stuffing' => "'stuffing"
    ], "id LIKE '16000%'", 10, 'order by id');
    echo $updstr.PHP_EOL;
    $res = $driver->execute_query($updstr);
    if ($res) {
        echo $res->affected_rows().PHP_EOL;
    } else {
        $error = $driver->error();
        print_r($error);
    }


    $insstr = $driver->insert_string('tb_page', [
        'id' => 209, 'value' => 'Soy charcoal 35', 'stuffing' => '1234567890'
    ]);
    echo $insstr.PHP_EOL;
    $res = $driver->execute_query($insstr);
    if ($res) {
        echo $res->affected_rows().PHP_EOL;
    }


    $res = $driver->execute_query('delete from tb_page where id =209');
    if ($res) {
        echo $res->affected_rows().PHP_EOL;
    }

    if (($count = $driver->count_all('tb_page')) > 0) {
        echo "records => $count".PHP_EOL;
    }

    $cols = $driver->primary_key('tb_page');
    if ($cols) {
        print_r($cols);
    }

    $driver->close();
}
