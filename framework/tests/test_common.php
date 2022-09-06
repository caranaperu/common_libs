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
    $flc->validation->set_message('rule_id','El mensaje %s viene de set_mesage');

} else {
    $flc->validation->set_message(['rule_id'=>'El mensaje %s viene de set_mesage','required' => 'Es requerido {field}']);

}


$flc->validation->add_rule('message2', 'Message2', [[$users_model, 'valid_username']]); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message3', 'Message3', [['rule_id',[$users_model, 'valid_username']]],['rule_id' => 'EL %s no sirve']); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message4', 'Message4', [['rule_id',function($str) {echo 'paso amomious '.PHP_EOL;return false;}]],['rule_id' => 'EL %s no sirve (v2)']); // esto solo funciona con add rule NOOO en el array.
$flc->validation->add_rule('message5', 'Message5', [['rule_id',function($str) {echo 'paso amomious 2'.PHP_EOL;return false;}]]); // esto solo funciona con add rule NOOO en el array.

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
echo 'greater than field => '.$flc->validation->greater_than_field('1.2','num01').PHP_EOL;
echo 'greater than field => '.$flc->validation->greater_than_field('1.06','num02').PHP_EOL;


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
