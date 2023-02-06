<?php

/**
 * Searches an array through dot syntax. Supports
 * wildcard searches, like foo.*.bar
 *
 * @return mixed
 */
function dot_array_search(string $index, array $array) {
    // See https://regex101.com/r/44Ipql/1
    $segments = preg_split('/(?<!\\\\)\./', rtrim($index, '* '), 0, PREG_SPLIT_NO_EMPTY);
    $segments = array_map(static fn($key) => str_replace('\.', '.', $key), $segments);

    return _array_search_dot($segments, $array);

}

/**
 * Used by `dot_array_search` to recursively search the
 * array with wildcards.
 *
 * @return mixed
 * @internal This should not be used on its own.
 *
 */
function _array_search_dot(array $indexes, array $array) {
    // Grab the current index
    $currentIndex = $indexes ? array_shift($indexes) : null;

    if ((empty($currentIndex) && (int)$currentIndex !== 0) || (!isset($array[$currentIndex]) && $currentIndex !== '*')) {
        return null;
    }

    // Handle Wildcard (*)
    if ($currentIndex === '*') {
        $answer = [];

        foreach ($array as $value) {
            if (!is_array($value)) {
                return null;
            }

            $answer[] = _array_search_dot($indexes, $value);
        }

        $answer = array_filter($answer, static fn($value) => $value !== null);

        if ($answer !== []) {
            if (count($answer) === 1) {
                // If array only has one element, we return that element for BC.
                return current($answer);
            }

            return $answer;
        }

        return null;
    }

    // If this is the last index, make sure to return it now,
    // and not try to recurse through things.
    if (empty($indexes)) {
        return $array[$currentIndex];
    }

    // Do we need to recursively search this value?
    if (is_array($array[$currentIndex]) && $array[$currentIndex] !== []) {
        return _array_search_dot($indexes, $array[$currentIndex]);
    }

    // Otherwise, not found.
    return null;
}

function array_dot_search_2(string $index, array $array): ?array {

    if (empty($index) || count($array) == 0) {
        return null;
    }

    $akeys = array_keys($array);
    // get the keys that fall in the search.
    $akeys = preg_grep("/$index/i", $akeys);

    $data = [];
    if ($akeys) {
        foreach ($akeys as $key) {
            $data[] = $array[$key];
        }

        // return the values.
        return $data;
    }

    return null;
}


$a = [
    'cli_standar_test' => 'cst',
    'cli_standar_test2' => 'cst2',
    'foo_standar_bar' => 'foobar',
    'xxxxxxx' => 'xxx'

];
echo '******************************************> a'.PHP_EOL;
print_r($a);

$b['cli_standar_test'] = 'cst';
$b['cli_standar_test2'] = 'cst2';
$b['foo_standar_bar'] = 'foobar';
$b['xxxxxxx'] = 'xxxx';

echo '******************************************> a'.PHP_EOL;
print_r($b);

$ab = array_keys($b);

echo '******************************************> ab - array_keys'.PHP_EOL;
print_r($ab);


$array = preg_grep("/foo.*.bar/i", $ab);

print_r($array);

$array = preg_grep("/cli_standar_test/i", $ab);

print_r($array);

$answer = array_dot_search_2("foo.*.bar", $a);
print_r($answer);

$answer = array_dot_search_2("cl.*.tes", $a);
print_r($answer);

$answer = array_dot_search_2(".tes", $a);
print_r($answer);

$answer = array_dot_search_2("xxx.*", $a);
print_r($answer);


$a = [];
$a[] = ['Test','test',true];
$a[] = ['Test2','test2',true];
$a[] = ['Test3','test3',true];

print_r($a);

foreach ($a as $key=>$values) {
    echo '------------------------------------'.PHP_EOL;
    print_r( $values).PHP_EOL;
    if ($values[1] == 'Test3') {
        echo 'FOUND'.PHP_EOL;
    }
}

print_r(array_map('array_shift', $a));

//echo array_column($a,'test2');
//echo array_search('test2',array_column($a,'test2'));
