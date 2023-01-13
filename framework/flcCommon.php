<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 *
 */

namespace framework;

use Exception;
use framework\core\flcConfig;
use framework\core\flcServiceLocator;
use RuntimeException;


/**
 * Common functions library
 */
class flcCommon {

    /**
     * Load the config file that need to be in the APPPATH defined and
     * config directory.
     * This method can be used to load the main config file directly
     * without the use of any other class of the library, if we need to load the other
     * config files
     *
     * @return flcConfig instance with the config options
     * @throws Exception
     *
     * if fails throw  an exception.
     *
     */
    static private function &_load_config(): flcConfig {
        /**
         * @var flcConfig $config
         */
        static $config;

        // only load one time
        if (!isset($config) || !$config) {
            $config = new flcConfig();

            if ($config !== null) {
                // load the default config.
                if ($config->load_config() === null) {
                    flcCommon::log_message('error', 'FLC->initialize - Cant load main config');
                    throw new RuntimeException('flcCommon->load_config - Cant load main config - COM0002');
                }
            }
        }

        return $config;

    }

    /**
     * @return flcConfig the config instance containing at least the main config.
     * @throws Exception
     */
    static function &get_config(): flcConfig {
        return self::_load_config();

    }

    // --------------------------------------------------------------------

    /**
     * Load the validation config file that need to be in the APPPATH defined and
     * config directory.
     * The validation file will be searched on the config subdirectory 'validation'.
     *
     * @return array of rules or group of rules or null if file doesnt exist.
     * @throws RuntimeException
     */
    static function load_validation_config(string $p_filename): array {

        // only load one time
        $path = APPPATH."config/validation/$p_filename.php";
        if (file_exists($path)) {
            require $path;
            if (isset($config)) {
                return $config;
            }
        }

        throw new RuntimeException("The validation file specified [ $p_filename ] , doesnt exist or not contains a config file - COM0001");
    }

    // --------------------------------------------------------------------


    /**
     * Is HTTPS?
     *
     * Determines if the application is accessed via an encrypted
     * (HTTPS) connection.
     *
     * Try all the well known ways to check the protocol, some servers
     * use one or another
     *
     * @return    bool
     */
    static function is_https(): bool {
        if (!self::is_cli()) {
            if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {
                return true;
            } elseif (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') {
                return true;
            } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {
                return true;
            } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {
                return true;
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_SSL']) && strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) !== 'on') {
                return true;
            } elseif ($_SERVER['SERVER_PORT'] == 443) {
                return true;
            }

        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Is CLI?
     *
     * Test to see if a request was made from the command line.
     *
     * @return    bool
     */
    static function is_cli(): bool {
        return (php_sapi_name() === 'cli' or defined('STDIN'));
    }

    // --------------------------------------------------------------------

    /**
     * Is AJAX request?
     *
     * Test to see if a request contains the HTTP_X_REQUESTED_WITH header.
     *
     * @return    bool
     */
    static function is_ajax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    // --------------------------------------------------------------------


    /**
     * Remove Invisible Characters
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Java\0script.
     *
     * @param string $p_str the string to validate
     * @param bool   $p_url_encoded
     *
     * @return    string the result
     */
    static function remove_invisible_characters(string $p_str, bool $p_url_encoded = true): string {
        $non_displayables = [];

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($p_url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/i';    // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i';    // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127

        do {
            $p_str = preg_replace($non_displayables, '', $p_str, -1, $count);
        } while ($count);

        return $p_str;
    }

    // --------------------------------------------------------------------


    /**
     * Extract the controller name from an uri.
     * For web applications we expect a uri like this.
     * http(s)://host:port/index.php/controller_name?params.......
     *
     * For cli aplications we expect at least:
     *
     * php index.php controler_name param1=value1 .......
     *
     * @param string $p_uri with the uri , if the call is from a cli application
     * this parameter its ignored.
     *
     * @return string the controller name from the uri or the default one is not found.
     * @throws Exception
     */
    static function uri_get_controller(string $p_uri): string {
        $config = self::get_config();
        $default_controller = $config->item('default_controller');

        if (!self::is_cli()) { // web call

            if (($parsedDsn = @parse_url($p_uri)) !== false) {
                $url_explode = explode('/', $parsedDsn['path']);
                if (count($url_explode) <= 1) {
                    return $default_controller;
                } else {
                    $controller = $url_explode[count($url_explode) - 1];
                    // is truly a controller name or a php file
                    if (stripos($controller, '.php') || empty($controller)) {
                        return $default_controller;
                    }
                    return $controller;

                }

            }

            return '';

        } else {
            // if its cli
            $args = array_slice($_SERVER['argv'], 1);
            if ($args && count($args) > 1 && $args[0] === 'index.php') {
                return $args[1];
            } else {
                return $default_controller;

            }

        }

    }


    // --------------------------------------------------------------------

    /**
     * Returns the MIME types array from config/mimes.php
     * in APPPATH.
     *
     * @return    array
     */
    static function &get_mimes(): array {
        static $_mimes;

        if (empty($_mimes)) {
            if (file_exists(APPPATH.'config/mimes.php')) {
                $_mimes = include(APPPATH.'config/mimes.php');
            } else {
                $_mimes = [];
            }
        }

        return $_mimes;
    }


    // --------------------------------------------------------------------



    /**
     *
     * @param string $p_type The error level: 'error', 'debug' or 'info'
     * @param string $p_message
     *
     * @return void
     * @throws Exception
     */
    static function log_message(string $p_type, string $p_message) {
        //echo $p_type.' : '.$p_message.PHP_EOL;
        flcServiceLocator::get_instance()->service('log')->write_log($p_type, $p_message);

    }


}