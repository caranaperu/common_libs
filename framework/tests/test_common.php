<?php

use framework\core\FLC;
use framework\core\flcLanguage;
use framework\core\flcValidation;
use framework\flcCommon;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/framework';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);
const WEBAPP = false;
const LOADDB = true;


include_once BASEPATH."/flcCommon.php";
include_once BASEPATH."/core/FLC.php";
include_once BASEPATH."/core/flcLanguage.php";
include_once BASEPATH."/core/flcValidation.php";


function _my_error_handler(int $severity, string $message, string $filepath, int $line) {
    echo "severity = $severity , message = $message , filepath = $filepath, line =  $line".PHP_EOL;
}

function my_exception_handler(Throwable $exception) {
    print_r($exception);
}

function valid_username($name) {
    print_r('PASOOOOOOOO');

    return true;
}

set_error_handler('_my_error_handler');
set_exception_handler('my_exception_handler');

$flc = new FLC();

// Inicializacion
$flc->config = flcCommon::load_config();
$flc->DB = flcCommon::load_database();
print_r($flc->DB);

//$flc->DB = flcCommon::load_database('sql_server');
//print_r($flc->DB);

$flc->lang = new flcLanguage();
if (!$flc->lang->load(['email', 'calendar'], 'es')) {
    echo 'fail';
} else {
    echo $flc->lang->line('cal_november').PHP_EOL;
}

$xx = new xx();
$users_model = new xx();

$flc->validation = new flcValidation($flc->lang, $xx);

$flc->set_validations('test_validation');
$rules = $flc->get_rules('v_entidad_registro_propiedad');

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
$rules = $flc->get_rules();
print_r($rules);
echo '***************************************************'.PHP_EOL;
$flc->validation->set_rules($rules['email']);

$data = [
    /*  'title' => 'soy un titulo',*/
      'message' => 'soy un mensaje',
     'fieldtest' => 'soy field test',
    /*  'name' => 'soy un nombre',*/
    'message2' => 'soy un mensaje2',
    'message3' => 'soy un mensaje3',
    'message4' => 'soy un mensaje4',
    'message5' => 'soy un mensaje5',

   // 'fieldtest2' => 'soy field test2',

];

$flc->validation->set_data($data);
// PAra un callback el error de la regla si se indica no debe usar la palabra callbak de la regla.
$flc->validation->add_rule('fieldtest', 'Field Test', 'callback_fieldtest', [
    'fieldtest' => 'You must provide with callback a %s.',
]);

$simple_set_message = false;
if ($simple_set_message) {
    $flc->validation->set_message('rule_id','El mensaje %s viene de set_mesage');

} else {
    $flc->validation->set_message(['rule_id'=>'El mensaje %s viene de set_mesage','required' => 'Es requerido {field}']);

}


$flc->validation->add_rule('message2', 'Message2', [[$users_model, 'valid_username']]); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message3', 'Message3', [['rule_id',[$users_model, 'valid_username']]],['rule_id' => 'EL %s no sirve']); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message4', 'Message4', [['rule_id',function($str) {echo 'paso amomious ';return false;}]],['rule_id' => 'EL %s no sirve (v2)']); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message5', 'Message5', [['rule_id',function($str) {echo 'paso amomious 2';return false;}]]); // esto solo funciona con add rule NOOO en el array.

$flc->validation->run();


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
