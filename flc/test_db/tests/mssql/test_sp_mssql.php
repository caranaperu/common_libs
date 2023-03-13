<?php


$connectionInfo = ["Database" => "veritrade", "UID" => "sa", "PWD" => "202106"];
$conn = sqlsrv_connect('192.168.18.49', $connectionInfo);

echo PHP_EOL.'---------------------------- Devuelve single resultsets ------------------------'.PHP_EOL;

$res = sqlsrv_query($conn, "{CALL getResultset(1,2018)};");
while ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
    print_r($row);
}

echo PHP_EOL.'---------------------------- Devuelve single resultsets y out params ------------------------'.PHP_EOL;

$inparam = 2;
$params = array (array(&$inparam,SQLSRV_PARAM_OUT));

$res = sqlsrv_query($conn, "exec getResultset_out 1,2018,?;",$params);

if ($res) {
    $results = 0;
    do {

        printf("<b>Result #%u</b>:<br/>", ++$results);
        while ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
            print_r($row);
        }
    } while (sqlsrv_next_result($res));
    // con resultset siempre al final.
    echo 'El output parameter es : ',$inparam.PHP_EOL;

    sqlsrv_free_stmt($res);
} else {
    echo 'Error';
    $error = sqlsrv_errors();
    print_r($error).PHP_EOL;
}

echo PHP_EOL.'---------------------------- Devuelve multiples resultsets ------------------------'.PHP_EOL;

$res = sqlsrv_query($conn, "{CALL getMultipleResultset(1,2018)};");

if ($res) {
    $results = 0;

    do {

        printf("<b>Result #%u</b>:<br/>", ++$results);
        while ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
            print_r($row);
        }
    } while (sqlsrv_next_result($res));
    sqlsrv_free_stmt($res);

}

echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params ------------------------'.PHP_EOL;

$inparam = 2;
$params = array (array(&$inparam,SQLSRV_PARAM_OUT));

$res = sqlsrv_query($conn, "{CALL getMultipleResultset_out(1,2018,?)};",$params);

if ($res) {
    $results = 0;

    do {

        printf("<b>Result #%u</b>:<br/>", ++$results);
        while ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
            print_r($row);
        }
    } while (sqlsrv_next_result($res));
    // con resultset siempre al final.
    echo 'El output parameter es : ',$inparam.PHP_EOL;
    sqlsrv_free_stmt($res);

} else {
    echo 'Error';
    $error = sqlsrv_errors();
    print_r($error).PHP_EOL;
}

echo PHP_EOL.'---------------------------- Devuelve un solo valor ------------------------'.PHP_EOL;
$res = sqlsrv_query($conn, "declare @ret int;exec @ret = getSimpleValue 2;select  @ret");

if ($res) {
    $results = 0;

    do {

        printf("<b>Result #%u</b>:<br/>", ++$results);
        while ($row = sqlsrv_fetch_array($res, SQLSRV_FETCH_ASSOC)) {
            print_r($row);
        }
    } while (sqlsrv_next_result($res));
    sqlsrv_free_stmt($res);

} else {
    echo 'Error';
    $error = sqlsrv_errors();
    print_r($error).PHP_EOL;
}

echo PHP_EOL.'---------------------------- Devuelve un solo valor - out param (2) ------------------------'.PHP_EOL;
$inparam = 100;
$params = array (array(&$inparam,SQLSRV_PARAM_OUT));
$res = sqlsrv_query($conn, "{CALL assignDemo(?)};",$params);

if ($res) {
    echo 'El resultado es: '.$inparam.PHP_EOL;
    sqlsrv_free_stmt($res);

}  else {
    echo 'Error';
    $error = sqlsrv_errors();
    print_r($error).PHP_EOL;
}

echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) ------------------------'.PHP_EOL;
$inparam = 'test';
$inparam2 = 1;

$params = array (array(&$inparam,SQLSRV_PARAM_OUT),array(&$inparam2,SQLSRV_PARAM_OUT));
$res = sqlsrv_query($conn, "{CALL assignDemo_2(?,?)};",$params);


if ($res) {
    echo 'El resultado es: '.$inparam.PHP_EOL;
    echo 'El resultado es: '.$inparam2.PHP_EOL;
    sqlsrv_free_stmt($res);

}  else {
    echo 'Error';
    $error = sqlsrv_errors();
    print_r($error).PHP_EOL;
}

echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) ------------------------'.PHP_EOL;
$inparam = 'test';
$inparam2 = 1;

$params = array (array(&$inparam,SQLSRV_PARAM_OUT),array(&$inparam2,SQLSRV_PARAM_OUT));
$res = sqlsrv_query($conn, "{CALL assignDemo_3(?,333,?)};",$params);


if ($res) {
    echo 'El resultado es: '.$inparam.PHP_EOL;
    echo 'El resultado es: '.$inparam2.PHP_EOL;
    sqlsrv_free_stmt($res);

}  else {
    echo 'Error';
    $error = sqlsrv_errors();
    print_r($error).PHP_EOL;
}


// Tipo scalar , tipo resultset , tipo resultset_outp, tipo_multi_resultset, tipo_multi_resultset_outp

/*
$sp_name = $driver->callable_string('test', 'function', 'scalar',[
    ['p1',INOUY,SQLTYPE], 100, 1, false, true, 44, '44', 2000
], [0 => ['string', '(20)'], 2 => 'float', 3 => 'boolean', 5 => 'bit', 6 => 'bit', 7 => 'year']);
echo $sp_name.PHP_EOL;
*/

sqlsrv_close($conn);