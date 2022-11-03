<?php
$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/framework';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);
// views paths
define('VIEWPATH', APPPATH.'views'.DIRECTORY_SEPARATOR);

define('ENVIRONMENT', 'development');

include_once BASEPATH.'/flcAutoloader.php';


use framework\core\accessor\core\model\core\flcServiceLocator;



// flush any preconfigured buffering
if (ob_get_level()) ob_end_clean();


ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);







$log = flcServiceLocator::get_instance()->service('log');

$flc = \framework\core\accessor\core\model\core\FLC::get_instance();
$flc->execute_request();






