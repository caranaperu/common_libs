<?php

use flc\core\FLC;

use flc\core\flcLanguage;
use flc\core\flcServiceLocator;
use flc\core\flcValidation;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/flc';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);

// views paths
define('VIEWPATH', APPPATH.'views'.DIRECTORY_SEPARATOR);
define('WRITEPATH', APPPATH.'writable'.DIRECTORY_SEPARATOR);

include_once BASEPATH.'/flcAutoloader.php';


/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     testing
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 */
define('ENVIRONMENT', 'development');

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
//error_reporting(E_ALL);


// flush any preconfigured buffering
if (ob_get_level()) {
    ob_end_clean();
}



const WEBAPP = false;
const LOADDB = true;



function _my_error_handler(int $severity, string $message, string $filepath, int $line) {
    echo "severity = $severity , message = $message , filepath = $filepath, line =  $line".PHP_EOL;
}

function my_exception_handler(Throwable $exception) {
    print_r($exception);
}


// ------------------------------------------------------------------------

if (!function_exists('_shutdown_handler')) {
    /**
     * Shutdown Handler
     *
     * This is the shutdown handler that is declared at the top
     * of CodeIgniter.php. The main reason we use this is to simulate
     * a complete custom exception handler.
     *
     * E_STRICT is purposively neglected because such events may have
     * been caught. Duplication or none? None is preferred for now.
     *
     * @link    http://insomanic.me.uk/post/229851073/php-trick-catching-fatal-errors-e-error-with-a
     * @return    void
     */
    function _shutdown_handler() {
        $last_error = error_get_last();
        if (isset($last_error) && ($last_error['type'] & (E_ERROR | E_PARSE | E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_COMPILE_WARNING))) {
            _error_handler($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}


// --------------------------------------------------------------------

if (!function_exists('_error_handler')) {
    /**
     * Error Handler
     *
     * This is the custom error handler that is declared at the (relative)
     * top of CodeIgniter.php. The main reason we use this is to permit
     * PHP errors to be logged in our own log files since the user may
     * not have access to server logs. Since this function effectively
     * intercepts PHP errors, however, we also need to display errors
     * based on the current error_reporting level.
     * We do that with the use of a PHP error template.
     *
     * @param int    $severity
     * @param string $message
     * @param string $filepath
     * @param int    $line
     *
     * @return    void
     */
    function _error_handler($severity, $message, $filepath, $line) {
        $is_error = (((E_ERROR | E_PARSE | E_COMPILE_ERROR | E_CORE_ERROR | E_USER_ERROR) & $severity) === $severity);

        // When an error occurred, set the status header to '500 Internal Server Error'
        // to indicate to the client something went wrong.
        // This can't be done within the $_error->show_php_error method because
        // it is only called when the display_errors flag is set (which isn't usually
        // the case in a production environment) or when errors are ignored because
        // they are above the error_reporting threshold.
        if ($is_error) {
            set_status_header(200);
        }

        // Should we ignore the error? We'll get the current error_reporting
        // level and add its bits with the severity bits to find out.
        if (($severity & error_reporting()) !== $severity) {
            return;
        }

        $_error =& load_class('Exceptions', 'core');
        $_error->log_exception($severity, $message, $filepath, $line);

        // Should we display the error?
        if (str_ireplace(['off', 'none', 'no', 'false', 'null'], '', ini_get('display_errors'))) {
            $_error->show_php_error($severity, $message, $filepath, $line);
        }

        // If the error is fatal, the execution of the script should be stopped because
        // errors can't be recovered from. Halting the script conforms with PHP's
        // default error handling. See http://www.php.net/manual/en/errorfunc.constants.php
        if ($is_error) {
            exit(1); // EXIT_ERROR
        }
    }
}

function valid_username($name) : bool {
    print_r('PASOOOOOOOO');

    return true;
}

//set_error_handler('_my_error_handler');
//set_exception_handler('my_exception_handler');
//register_shutdown_function('_shutdown_handler');

$flc = FLC::get_instance();

// ver utf-8

// Inicializacion
//$flc->config = new flcConfig();
//$configs = $flc->config->load_config();
//print_r($flc->get_config()).PHP_EOL;


$flc->db = flcServiceLocator::get_instance()->service('database');
print_r($flc->db);


$flc->lang = new flcLanguage();
if (!$flc->lang->load(['email', 'calendar'], 'es')) {
    echo 'fail';
} else {
    echo $flc->lang->line('cal_november').PHP_EOL;
}

$xx = new xx();
$users_model = new xx();

$flc->validation = new flcValidation($flc->lang, $xx);

// test 1 ,  load 2 validations with descriptors

// casi uno named arrays
$flc->set_validations('test_validation');
$flc->set_validations('perfil_validation', false); // false to append
$vals = $flc->get_validations();

echo '************ validations from test_validation+perfil_validation'.PHP_EOL;
print_r($vals); // Aqui encontraremos un arreglo con 2 entradas , c/u corresponde al de cada archivo

echo '************ validation group v_entidad_registro_propiedad from test_validation'.PHP_EOL;
$rules = $flc->get_rules('v_entidad_registro_propiedad');
print_r($rules);

echo '************ rules group v_entidad_registro_propiedad/updEntidadRegistroPropiedad from test_validation'.PHP_EOL;
$rules = $flc->get_rules('v_entidad_registro_propiedad', 'updEntidadRegistroPropiedad');
print_r($rules);


echo '************ validation group v_perfil from test_validation'.PHP_EOL;
$rules = $flc->get_rules('v_perfil'); // este viene de test_validation
print_r($rules); // Aqui encontraremos un arreglo con 2 entradas , c/u corresponde al de cada archivo

echo '************ rules group v_perfil/getPerfil from test_validation'.PHP_EOL;
$rules = $flc->get_rules('v_perfil', 'getPerfil'); // este viene de test_validation
print_r($rules); // Aqui encontraremos un arreglo con 2 entradas , c/u corresponde al de cada archivo


// Caso 2
$flc->set_validations('test2_validation'); // new validations , reset
$flc->set_validations('test3_validation', false); // append , no reset

echo '************ validations from test2_validation/test3_validation'.PHP_EOL;
$vals = $flc->get_validations();
print_r($vals); // Aqui encontraremos un arreglo con 1 entrada default y 2 la acumulacion de los 2 archivos como subtentradas


echo '************ det Default group validations'.PHP_EOL;
$rules = $flc->get_rules('default'); // este viene de test_validation
print_r($rules); // Aqui encontraremos un arreglo con 2 entradas , c/u corresponde al de cada archivo

echo '************ det Default group validations/ email_2 rules'.PHP_EOL;
$rules = $flc->get_rules('default', 'email_2'); // este viene de test_validation
print_r($rules); // Aqui encontraremos un arreglo con 2 entradas , c/u corresponde al de cada archivo


// Caso 3 , mixed named array and simple array

$flc->set_validations('test_validation');
$flc->set_validations('test2_validation', false); // false to append
$vals = $flc->get_validations();

echo '************ validations from test_validation+test2_validation'.PHP_EOL;
print_r($vals); // Aqui encontraremos un arreglo con 2 entradas , c/u corresponde al de cada archivo


echo '***************************************************'.PHP_EOL;
echo '***************************************************'.PHP_EOL;
echo '***************************************************'.PHP_EOL;
echo '***************************************************'.PHP_EOL;
echo '***************************************************'.PHP_EOL;

$rules = $flc->get_rules('v_entidad_registro_propiedad', false); // false reset validations
echo '***************************************************'.PHP_EOL;
print_r($rules);
$flc->validation->set_rules($rules['updEntidadRegistroPropiedad']);

$flc->set_validations('perfil_validation', false);
$rules = $flc->get_rules('v_perfil');

echo '***************************************************'.PHP_EOL;
print_r($rules);
$flc->validation->set_rules($rules['getPerfil']);


echo '***************************************************'.PHP_EOL;
echo '***************************************************'.PHP_EOL;
$flc->set_validations('test2_validation');
$rules = $flc->get_rules('default');
print_r($rules);
echo '***************************************************'.PHP_EOL;
$flc->validation->set_rules($rules['email_2']);

$data = [
    /*  'title' => 'soy un titulo',*/
    'message' => 'soy un mensaje',
    'fieldtest' => 'soy field test',
    /*  'name' => 'soy un nombre',*/
    'message2' => 'soy un mensaje2',
    'message3' => 'soy un mensaje3',
    'message4' => 'soy un mensaje4',
    'message5' => 'soy un mensaje5',
    'num01' => '0',
    'num02' => '1.05',
    'fecha01' => '08-07-2022',
    'fecha02' => '09-07-2022',
    'fecha03' => '07-07-2022',
    'is_a_bool' => '0',
    'is_a_bool2' => '1',
    'f_check_with_bool' => 'avalue'

    // 'fieldtest2' => 'soy field test2',

];

$flc->validation->set_data($data);
// PAra un callback el error de la regla si se indica no debe usar la palabra callbak de la regla.
$flc->validation->add_rule('fieldtest', 'Field Test', 'callback_fieldtest', [
    'fieldtest' => 'You must provide with callback a %s.',
]);

$simple_set_message = false;
if ($simple_set_message) {
    $flc->validation->set_message('rule_id', 'El mensaje %s viene de set_mesage');

} else {
    $flc->validation->set_message([
        'rule_id' => 'El mensaje %s viene de set_mesage',
        'required' => 'Es requerido {field}'
    ]);

}


$flc->validation->add_rule('message2', 'Message2', [
    [
        $users_model,
        'valid_username'
    ]
]); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message3', 'Message3', [
    [
        'rule_id',
        [$users_model, 'valid_username']
    ]
], ['rule_id' => 'EL %s no sirve']); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message4', 'Message4', [
    [
        'rule_id',
        function ($str) {
            echo 'paso amomious '.PHP_EOL;

            return false;
        }
    ]
], ['rule_id' => 'EL %s no sirve (v2)']); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message5', 'Message5', [
    [
        'rule_id',
        function ($str) {
            echo 'paso amomious 2'.PHP_EOL;

            return false;
        }
    ]
]); // esto solo funciona con add rule NOOO en el array.

$flc->validation->add_rule('num01', 'num01', "in_list[100,200,0]|greater_than_field[num02]");
$flc->validation->add_rule('num02', 'num02', "less_than_field[num01]");

$flc->validation->add_rule('fecha01', 'fecha01', "is_future_date[fecha02]");
$flc->validation->add_rule('fecha02', 'fecha02', "is_future_date[fecha01]");
$flc->validation->add_rule('fecha03', 'fecha03', "is_future_date[fecha02]");

$flc->validation->add_rule('f_check_with_bool', 'f_check_with_bool', "depends_on_boolean[is_a_bool]");
$flc->validation->add_rule('is_a_bool2', 'is_a_bool2', "is_boolean|matches[is_a_bool]|differs[is_a_bool]");


if (!$flc->validation->run()) {
    $errors = $flc->validation->error_array();
    foreach ($errors as $field => $error) {
        echo "Field $field has an error = $error".PHP_EOL;
    }
}

echo 'Test decimal original'.PHP_EOL;
echo '10.12 => '.$flc->validation->decimal('10.12').PHP_EOL;
echo '10.1 => '.$flc->validation->decimal('10.1').PHP_EOL;
echo '0.1 => '.$flc->validation->decimal('0.1').PHP_EOL;
echo '10. => '.$flc->validation->decimal('10.').PHP_EOL;
echo '10 => '.$flc->validation->decimal('10').PHP_EOL;
echo '. => '.$flc->validation->decimal('.').PHP_EOL;
echo '10..12 => '.$flc->validation->decimal('10..12').PHP_EOL;
echo '10.12. => '.$flc->validation->decimal('10.12.').PHP_EOL;
echo 'A => '.$flc->validation->decimal('A').PHP_EOL;


echo 'Test omlyValidText'.PHP_EOL;
echo 'Test => '.$flc->validation->valid_code('Test').PHP_EOL;
echo '0Test => '.$flc->validation->valid_code('0Test').PHP_EOL;
echo '/Test => '.$flc->validation->valid_code('/Test').PHP_EOL;
echo '_Test => '.$flc->validation->valid_code('_Test').PHP_EOL;
echo 'Test_ => '.$flc->validation->valid_code('Test_').PHP_EOL;
echo 'Test_2 => '.$flc->validation->valid_code('Test_2').PHP_EOL;
echo ' Test_2 => '.$flc->validation->valid_code(' Test_2').PHP_EOL;
echo '00 009 => '.$flc->validation->valid_code('00 009').PHP_EOL;
echo 'Carlos Arana  => '.$flc->validation->valid_code('Carlos Arana ').PHP_EOL;
echo 'Carlos Arana => '.$flc->validation->valid_code('Carlos Arana').PHP_EOL;
echo 'Carlos Araña => '.$flc->validation->valid_code('Carlos Araña').PHP_EOL;
echo '0000-0001 => '.$flc->validation->valid_code('0000-0001').PHP_EOL;
echo '0000_0001 => '.$flc->validation->valid_code('0000_0001').PHP_EOL;
echo '-0000-0001 => '.$flc->validation->valid_code('-0000-0001').PHP_EOL;
echo '0000.0001 => '.$flc->validation->valid_code('0000.0001').PHP_EOL;
echo '0000..0001 => '.$flc->validation->valid_code('0000..0001').PHP_EOL;
echo '0000--0001 => '.$flc->validation->valid_code('0000--0001').PHP_EOL;
echo '0000__0001 => '.$flc->validation->valid_code('0000__0001').PHP_EOL;
echo '0000/0001 => '.$flc->validation->valid_code('0000/0001').PHP_EOL;
echo '0000//0001 => '.$flc->validation->valid_code('0000//0001').PHP_EOL;
echo '0000@0001 => '.$flc->validation->valid_code('0000@0001').PHP_EOL;
echo '0000@@0001 => '.$flc->validation->valid_code('0000@@0001').PHP_EOL;
echo '0000&0001 => '.$flc->validation->valid_code('0000&0001').PHP_EOL;
echo '0000=0001 => '.$flc->validation->valid_code('0000=0001').PHP_EOL;
echo '0000?0001 => '.$flc->validation->valid_code('0000?0001').PHP_EOL;
echo '0000/00.01 => '.$flc->validation->valid_code('0000/00.01').PHP_EOL;
echo 'ñaña => '.$flc->validation->valid_code('ñaña').PHP_EOL;
echo 'ñaca => '.$flc->validation->valid_code('ñaca').PHP_EOL;
echo 'caña => '.$flc->validation->valid_code('caña').PHP_EOL;


echo PHP_EOL;
echo PHP_EOL.'Test Date'.PHP_EOL;
echo 'http://flabcor.com => '.$flc->validation->valid_url('http://flabcor.com').PHP_EOL;
echo 'http://flabcor.com/index => '.$flc->validation->valid_url('http://flabcor.com/index').PHP_EOL;
echo 'http://flabcor.com/index?a=1 => '.$flc->validation->valid_url('http://flabcor.com?a=1').PHP_EOL;
echo 'https://flabcor.com/index?a=1&b=2 => '.$flc->validation->valid_url('https://flabcor.com?a=1&b=2').PHP_EOL;
echo 'https://flabcor.com/index#aa?a=1&b=2 => '.$flc->validation->valid_url('https://flabcor.com/index#aa?a=1&b=2').PHP_EOL;
echo 'ftp://flabcor.com => '.$flc->validation->valid_url('ftp://flabcor.com').PHP_EOL;
echo 'ftp://flabcor.com/miword.doc => '.$flc->validation->valid_url('ftp://flabcor.com/miword.doc').PHP_EOL;
echo 'ftp:///flabcor.com/miword.doc => '.$flc->validation->valid_url('ftp:///flabcor.com/miword.doc').PHP_EOL;

// greater the field
echo 'greater than field => '.$flc->validation->greater_than_field('1.2', 'num01').PHP_EOL;
echo 'greater than field => '.$flc->validation->greater_than_field('1.06', 'num02').PHP_EOL;


// test flc execute_request
echo 'MB_ENABLED DEFINED ? = '.defined('MB_ENABLED').PHP_EOL;

$flc->execute_request();
echo 'MB_ENABLED = '.MB_ENABLED.PHP_EOL;
echo 'MB_ENABLED DEFINED ? = '.defined('MB_ENABLED').PHP_EOL;

/*$regexp =  '/^[A-Za-z0-9]+([- _\/.ñÑ]*[A-Za-z0-9])+$/';
if (preg_match($regexp, 'Ol/gg.hh- l')) {
    echo PHP_EOL.'cumple'.PHP_EOL;
}*/

class xx {
    public function fieldtest($param): bool {
        echo 'paso field test con param '.$param.PHP_EOL;

        return false;
    }

    public function valid_username($param): bool {
        echo 'paso valid user name '.$param.PHP_EOL;

        return false;
    }
}
//print_r($flc);
