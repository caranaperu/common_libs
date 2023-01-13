<?php

class test {
    public string $_callable_function_sep_string         = '()xxxx';
    public array  $_callable_procedure_sep_string        = ['(', ')'];

}

$t = new test();

echo 'procedure : '.$t->_callable_procedure_sep_string[1].PHP_EOL;
echo 'function : '.$t->_callable_function_sep_string[1].PHP_EOL;
echo 'function : '.$t->_callable_function_sep_string[2].PHP_EOL;

$var = "char(10)='10'";

$parts = explode('=',$var);

print_r($parts);
echo PHP_EOL;

$var = "char(10)";

$parts = explode('=',$var);

print_r($parts);
echo PHP_EOL;

$parts = explode('(',$var);

print_r($parts);
echo PHP_EOL;
