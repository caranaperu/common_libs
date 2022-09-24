<?php

use framework\core\flcServiceLocator;

$application_folder = dirname(__FILE__);
$system_path = '/var/www/common/framework';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);
// views paths
define('VIEWPATH', APPPATH.'views'.DIRECTORY_SEPARATOR);

define('ENVIRONMENT', 'development');

// flush any preconfigured buffering
if (ob_get_level()) ob_end_clean();

// obligatorios para empezar cualquier aplicacion
require_once BASEPATH."/core/FLC.php";

ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);

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
     * @param int $severity
     * @param string $message
     * @param string $filepath
     * @param int $line
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


set_error_handler('_my_error_handler');
set_exception_handler('my_exception_handler');
register_shutdown_function('_shutdown_handler');


/*echo ob_get_level().PHP_EOL;
ob_start();
echo ob_get_level().PHP_EOL;
ob_start();
echo ob_get_level().PHP_EOL;
ob_start();
echo ob_get_level().PHP_EOL;
ob_end_flush();
echo ob_get_level().PHP_EOL;
ob_start();
echo ob_get_level().PHP_EOL;
exit();

*/

$flc = \framework\core\FLC::get_instance();
$flc->execute_request();

//flcServiceLocator::get_instance()->service('views','/viewout');




