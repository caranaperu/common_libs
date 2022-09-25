<?php

namespace framework\core;

use Exception;
use framework\core\flcOutput;
use framework\database\driver\flcDriver;
use framework\flcCommon;
use RuntimeException;

require_once dirname(__FILE__).'/flcConfig.php';
require_once dirname(__FILE__).'/../flcCommon.php';
require_once dirname(__FILE__).'/../database/driver/flcDriver.php';
require_once dirname(__FILE__).'/flcUtf8.php';
require_once dirname(__FILE__).'/flcRequest.php';
require_once dirname(__FILE__).'/flcServiceLocator.php';
require_once dirname(__FILE__).'/flcOutput.php';
require_once dirname(__FILE__).'/flcLanguage.php';

class FLC {
    /**
     * Reference to the FLC singleton
     *
     * @var    FLC
     */
    private static FLC $_instance;

    public flcConfig $config;

    /**
     * The default db driver instance for the framework
     *
     * @var flcDriver
     */
    public flcDriver $DB;

    public flcLanguage $lang;

    public flcValidation $validation;

    public flcOutput $output;

    private array $_vrules         = [];
    private bool  $_is_initialized = false;

    /**
     * Class constructor
     *
     * @return    void
     * @throws Exception if initialize fail.
     */
    protected function __construct() {
        self::$_instance =& $this;
        $this->initialize();

    }

    public static function &get_instance(): FLC {
        static $loaded = false;
        if (!$loaded) {
            new FLC();
            $loaded = true;
        }

        return self::$_instance;
    }


    /**
     * @return bool
     * @throws Exception when trying to load the main config class fails.
     */
    public function initialize(): bool {
        // check if required extensions are loaded , this library is
        // for php 7+ , is rare its not loaded , if they are not loaded
        // an exception is send because are required by the validation
        // and output libraries.
        if (extension_loaded('mbstring')) {
            define('MB_ENABLED', true);
        } else {
            define('MB_ENABLED', false);
        }

        // There's an ICONV_IMPL constant, but the PHP manual says that using
        // iconv's predefined constants is "strongly discouraged".
        if (extension_loaded('iconv')) {
            define('ICONV_ENABLED', true);
        } else {
            define('ICONV_ENABLED', false);
        }

        if (!MB_ENABLED || !ICONV_ENABLED) {
            throw new RuntimeException('This library rqquire the mb_string and iconv extensions - FLC0001');
        }

        // Only can be initialized one time
        if (!$this->_is_initialized) {
            if ($this->config = new flcConfig()) {
                if ($this->config->load_config()) {
                    $this->_is_initialized = true;

                    return true;
                }
            }
            flcCommon::log_message('error', 'FLC->initialize - Cant load main config');

            throw new Exception('FLC->initialize - Cant load main config');
        } else {
            return true;
        }

    }

    /**
     * @return flcConfig|null
     * @throws Exception when trying to load the main config class fails.
     */
    public function &get_config(): ?flcConfig {
        if (!isset($this->config)) {
            $this->initialize();
        }

        return $this->config;

    }

    /*************************************************************************
     * Validation
     */


    /**
     * Load the validation file specified by the parameter p_filename to do validations after that.
     * Accept to 2 types of arrays on the files attached to the $config variable.
     * Type 1 - single array per file:
     *
     *            $config = [
     *               'signup' => [
     *                  [
     *                      'field' => 'username',
     *                      'label' => 'Username',
     *                      'rules' => 'required'
     *                  ],
     *                  [
     *                      'field' => 'password',
     *                      'label' => 'Password',
     *                      'rules' => 'required'
     *                  ]
     *               ],
     *               'email' => [
     *                   [
     *                       'field' => 'emailaddress',
     *                       'label' => 'EmailAddress',
     *                       'rules' => 'required|valid_email'
     *                   ],
     *                   [
     *                       'field' => 'name',
     *                       'label' => 'Name',
     *                       'rules' => 'required|max_length[2]|alpha'
     *                   ],
     *               ]
     *               ];
     * can contain multiple rules groups but not validation groups, in this case signup and email are rule groups.
     *
     * Type 2 - Named validation group with multiple rules grop.
     *            $config['email_validations] = [
     *               'signup' => [
     *                  [
     *                      'field' => 'username',
     *                      'label' => 'Username',
     *                      'rules' => 'required'
     *                  ],
     *                  [
     *                      'field' => 'password',
     *                      'label' => 'Password',
     *                      'rules' => 'required'
     *                  ]
     *               ],
     *               'email' => [
     *                   [
     *                       'field' => 'emailaddress',
     *                       'label' => 'EmailAddress',
     *                       'rules' => 'required|valid_email'
     *                   ],
     *                   [
     *                       'field' => 'name',
     *                       'label' => 'Name',
     *                       'rules' => 'required|max_length[2]|alpha'
     *                   ],
     *               ]
     *               ];
     *
     * can contain multiple rules groups and a validations  group.
     * In this case signup and email are rule groups and email_validations the validation group.
     *
     * In the case of type 1 , the method create a group validation named 'default'.
     *
     * Can read and merge type 1 and type 2 and create and array with multiple validations groups
     * and one of them will be named default.
     *
     * Of course , can add multiples files of the same type also.
     *
     * IS a requirement that each file of type 2 contain only one validation group per file.
     *
     * @param string $p_filename the filename of the file with the validation definition, this file need to end with
     * _validation.
     *
     * @param bool   $p_reset if true rest the array of validations and create a new one
     *
     * @return bool true if all went ok
     *
     * @see flcCommon::load_validation_config()
     */
    public function set_validations(string $p_filename, bool $p_reset = true): bool {

        $validations = flcCommon::load_validation_config($p_filename);
        if (isset($validations)) {
            $nelems = count($validations);

            if ($nelems == 0) {
                flcCommon::log_message('error', 'FLC->set_validations : No an array found as validations ');

                return false;
            }

            if ($p_reset) {
                $this->_vrules = [];
            }

            // if number of elements is > 1 , means that validations are not associated to a validation
            // group. (see remarks).
            if ($nelems > 1) {
                if (!isset($this->_vrules['default'])) {
                    $this->_vrules['default'] = [];
                }
                $this->_vrules['default'] = array_merge($this->_vrules['default'], $validations);
            } else {
                if (!$p_reset) {
                    $this->_vrules = array_merge($this->_vrules, $validations);
                } else {
                    $this->_vrules = $validations;
                }

            }

        }

        return true;
    }

    /**
     * Get the current validations loaded.
     *
     * @return array of validation groups/rules
     */
    public function get_validations(): array {
        if (count($this->_vrules) > 0) {
            return $this->_vrules;
        }

        return [];
    }

    /**
     * Get an specific group of rules associated to a group of validations.
     * If parameter p_rules is not specified return al rules groups associated
     *to one validation group.
     *
     * @param string $p_group the validations group name.
     * @param string $p_rules the rules group names.
     *
     * @return array
     */
    public function get_rules(string $p_group, string $p_rules = ''): array {
        if (count($this->_vrules) > 0) {
            if ($p_rules != '') {
                if (isset($this->_vrules[$p_group][$p_rules])) {
                    return $this->_vrules[$p_group][$p_rules];
                }

            } else {
                return $this->_vrules[$p_group];
            }
        }

        return [];
    }

    /**
     * @return void
     * @throws Exception multiple . check error code
     */
    public function execute_request() {

        // charset stuff
        $charset = strtoupper($this->get_config()->item('charset'));
        ini_set('default_charset', $charset);

        // mb_string stuff
        //
        // At this point MB_ENABLED will be tru , otherwise the instance of this clas can be
        // executed correctly and throw and exception.
        // mbstring.internal_encoding is deprecated starting with PHP 5.6
        // set only if exists.
        if (ini_get("mbstring.internal_encoding")) {
            ini_set("mbstring.internal_encoding", null);
        }
        // This is required for mb_convert_encoding() to strip invalid characters.
        // That's utilized by CI_Utf8, but it's also done for consistency with iconv.
        mb_substitute_character('none');

        // iconv stuff
        // At this point ICONV_ENABLED will be tru , otherwise the instance of this clas can be
        // executed correctly and throw and exception.
        // There's an ICONV_IMPL constant, but the PHP manual says that using
        // iconv's predefined constants is "strongly discouraged".
        // iconv.internal_encoding is deprecated starting with PHP 5.6
        // set only if exists.
        if (ini_get("iconv.internal_encoding")) {
            ini_set("iconv.internal_encoding", null);
        }

        // this library if for php >= 7
        ini_set('php.internal_encoding', $charset);

        // To setup constant UTF8_ENABLED
        flcUtf8::initialize();

        // get controller class name from uri.
        $uri = !flcCommon::is_cli() ? (flcCommon::is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : '';
        $controller_class = flcCommon::uri_get_controller($uri);

        // load the output manager
        $this->output = new flcOutput();

        // load the request
        $request = new flcRequest();

        // create controller and initialize.
        $controller = flcServiceLocator::get_instance()->service('controller', $controller_class);
        $controller->initialize($request);
        $controller->index();

        // get the output buffer to display
        $to_display = $this->output->get_final_output();

        // Send the output to the browser.
        // Does the controller contain a function named _output()?
        // If so send the output there.  Otherwise, echo it.
        if (method_exists($controller, 'output')) {
            $controller->output($to_display);
        } else {
            echo $to_display; // Send it to the browser!
        }

    }

}