<?php

include_once '/var/www/common/flc/core/accessor/flcPersistenceAccessor.php';
include_once '/var/www/common/flc/core/accessor/flcDbAccessor.php';


function array_search_partial($arr, $keyword) : array {
    $found = [];
    foreach($arr as $index => $string) {
        $the_parts = explode('\\',$string);
        $theClass = end($the_parts);

        if ($theClass === $keyword)
            $found[]=$string;
    }
    return $found;
}

$class_name = 'Exception';
$classess = get_declared_classes();
print_r($classess);
$found = array_search_partial($classess,$class_name);
unset($classess);

print_r($found);

if (count($found) > 1) {
    echo 'Tratar metodo alterno'.PHP_EOL;
} else {
    $theParts = explode($class_name,$found[0]);
    print_r($theParts[0]);
}


//print_r($theClasses);
//print_r(get_declared_classes());