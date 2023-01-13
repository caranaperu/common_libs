<?php


/*
 n practice there is another difference I don't see in other answers.
You could need to use mysqli_real_query() for a CALL statement.

If you are calling a stored procedure, then it can return more than one result.
mysqli_query() will fetch the first result, but could be more results that needs to be fetched,
and that would cause an error. You need to use mysqli_real_query(or mysqli_multi_query()) to fetch those result sets.
 */

$mysqli = new mysqli("localhost", "root", "melivane", "db_tests");

echo PHP_EOL.'---------------------------- Devuelve single resultsets ------------------------'.PHP_EOL;
$res = $mysqli->real_query("CALL getResultSet(1);");

if ($res) {
    $results = 0;
    do {
        if ($result = $mysqli->store_result()) {
            printf("<b>Result #%u</b>:<br/>", ++$results);

            while ($row = $result->fetch_assoc()) {
                print_r($row);
            }
            $result->free_result();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

//  $res->close();
}

echo PHP_EOL.'---------------------------- Devuelve single resultsets y out params ------------------------'.PHP_EOL;
$res = $mysqli->multi_query("CALL getResultset_out(1,218,@outparam);select @outparam");

if ($res) {
    $results = 0;
    do {
        if ($result = $mysqli->store_result()) {
            printf("<b>Result #%u</b>:<br/>", ++$results);

            while ($row = $result->fetch_assoc()) {
                print_r($row);
            }
            $result->free_result();
        }
    } while ($mysqli->more_results() && $mysqli->next_result());

    //  $res->close();
}

echo PHP_EOL.'---------------------------- Devuelve multiple resultsets ------------------------'.PHP_EOL;

$res = $mysqli->real_query("CALL getMultipleResultset(1)");

if ($res) {
    $results = 0;
    do {
        if ($result = $mysqli->store_result()) {
            printf("<b>Result #%u</b>:<br/>", ++$results);
            while ($row = $result->fetch_row()) {
                print_r($row);
            }
            $result->free_result();
            if ($mysqli->more_results()) {
                echo "<br/>";
            }
        }
    } while ($mysqli->next_result());
}


echo PHP_EOL.'---------------------------- Devuelve multiples resultsets y out params ------------------------'.PHP_EOL;

$res = $mysqli->multi_query("CALL getMultipleResultset_inout(1,@x);select @x;");

if ($res) {
    $results = 0;
    do {
        if ($result = $mysqli->store_result()) {
            printf("<b>Result #%u</b>:<br/>", ++$results);
            while ($row = $result->fetch_row()) {
                print_r($row);
            }
            $result->free_result();
            if ($mysqli->more_results()) {
                echo "<br/>";
            }
        }
    } while ($mysqli->next_result());
} else {
    echo 'Error';
    $error = $mysqli->error_list;
    print_r($error).PHP_EOL;
}


// En mysql para retornar un valor no se puede usar return se usa select reultado., solo las funciones retornan
// un valor unico.
echo PHP_EOL.'---------------------------- Devuelve un solo valor ------------------------'.PHP_EOL;

$res = $mysqli->real_query("CALL getSingleValue(100);");

if ($res) {
    $results = 0;
    do {
        if ($result = $mysqli->store_result()) {
            printf("<b>Result #%u</b>:<br/>", ++$results);
            while ($row = $result->fetch_row()) {
                print_r($row);
            }
            $result->free_result();
            if ($mysqli->more_results()) {
                echo "<br/>";
            }
        }
    } while ($mysqli->next_result());
}

echo PHP_EOL.'---------------------------- Devuelve un solo valor - out param (2) ------------------------'.PHP_EOL;

$res = $mysqli->multi_query("set @ret=2;CALL assignDemo(@ret);select @ret;");

if ($res) {
    $results = 0;
    do {
        if ($result = $mysqli->store_result()) {
            printf("<b>Result #%u</b>:<br/>", ++$results);
            while ($row = $result->fetch_row()) {
                print_r($row);
            }
            $result->free_result();
            if ($mysqli->more_results()) {
                echo "<br/>";
            }
        }
    } while ($mysqli->next_result());
}

echo PHP_EOL.'---------------------------- Devuelve 2 valores - out param (2) ------------------------'.PHP_EOL;


$res = $mysqli->multi_query("set @ret=2;set @ret2=100;CALL assignDemo_2(@ret,@ret2);select @ret,@ret2");

if ($res) {
    $results = 0;
    do {
        if ($result = $mysqli->store_result()) {
            printf("<b>Result #%u</b>:<br/>", ++$results);
            while ($row = $result->fetch_row()) {
                print_r($row);
            }
            $result->free_result();
            if ($mysqli->more_results()) {
                echo "<br/>";
            }
        }
    } while ($mysqli->next_result());
}

$mysqli->close();