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

$flc->validation = new flcValidation($xx);

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
    'title' => 'soy un titulo',
    'message' => 'soy un mensaje',
    'fieldtest' => 'soy field test'
];
$flc->validation->set_data($data);
$flc->validation->add_rule('fieldtest', 'Field Test', 'callback_fieldtest');

$flc->validation->run();


class xx {
    public function fieldtest($param) {
        echo 'paso field test con param '.$param.PHP_EOL;
        return true;
    }
}
//print_r($flc);
