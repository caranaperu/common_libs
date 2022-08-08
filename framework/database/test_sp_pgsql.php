<?php

function print_resultsets($driver, $res) {
    if ($res) {
        while ($row = pg_fetch_row($res)) {
            print_r($row);
        }
        pg_free_result($res);
    } else {
        print_r(pg_errormessage());
    }

}

$conn = pg_connect("host=192.168.18.30 port=5432 dbname=db_tests user=postgres password=melivane");

echo PHP_EOL.'---------------------------- Devuelve un solo valor ------------------------'.PHP_EOL;

$res = pg_query($conn, "select getSimpleValue(100)");
print_resultsets(null,$res);

echo PHP_EOL.'---------------------------- Devuelve single resultsets ------------------------'.PHP_EOL;

$res = pg_query($conn, "select * from getResultset();");
print_resultsets(null,$res);



echo PHP_EOL.'---------------------------- Devuelve out paramas desde un STORED PROCEDURE ------------------------'.PHP_EOL;

$res = pg_query($conn, "call assign_demo(1000,'En el programa');");
print_resultsets(null,$res);

echo PHP_EOL.'---------------------------- Devuelve multiples resultsets ------------------------'.PHP_EOL;
// CREATE OR REPLACE FUNCTION getMultipleResultset(who int,ref1 refcursor,ref2 refcursor)

$res = pg_query($conn, "begin;select from getMultipleResultset(3,'ref1','ref2');");

if ($res) {
    $res = pg_query($conn, "fetch all in ref1;");
    print_resultsets(null,$res);

    $res = pg_query($conn, "fetch all in ref2;");
    print_resultsets(null,$res);

    pg_query($conn, "end;");

} else {
    print_r(pg_errormessage());
}


echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params ------------------------'.PHP_EOL;
/*
$inparam = 2;
$params = [[&$inparam, SQLSRV_PARAM_OUT]];

$res = sqlsrv_query($conn, "{CALL getMultipleResultset_out(1,2018,?)};", $params);

if ($res) {
    $results = 0;

    do {

        printf("<b>Result #%u</b>:<br/>", ++$results);
        while ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
            print_r($row);
        }
    } while (sqlsrv_next_result($res));
    // con resultset siempre al final.
    echo 'El output parameter es : ', $inparam.PHP_EOL;
    sqlsrv_free_stmt($res);

} else {
    echo 'Error';
    $error = sqlsrv_errors();
    print_r($error).PHP_EOL;
}*/


echo PHP_EOL.'---------------------------- Devuelve un solo valor/ 2 valores - out param (2) ------------------------'.PHP_EOL;
$res = pg_query($conn, "select * from assignDemo(1);");

print_resultsets(null,$res);

pg_close($conn);