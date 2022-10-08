<?php

/**
 * This file is part of Future Labs Code 1 framework.
 **
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 */

namespace framework\core;

use Exception;
use framework\database\driver\flcDriver;
use framework\flcCommon;
use RuntimeException;

require_once dirname(__FILE__).'/flcConfig.php';
require_once dirname(__FILE__).'/../flcCommon.php';
require_once dirname(__FILE__).'/../database/driver/flcDriver.php';
require_once dirname(__FILE__).'/flcUtf8.php';
require_once dirname(__FILE__).'/flcRequest.php';
require_once dirname(__FILE__).'/flcServiceLocator.php';
require_once dirname(__FILE__).'/flcResponse.php';
require_once dirname(__FILE__).'/flcLanguage.php';

/**
 * The main superclass , load all requiered services to execute a request.
 * Can be constructed as usual , to obtain an instance use FLC::get_instance().
 *
 * Also do the bootstrap for each request using execute_request method.
 */
class FLC {
    /**
     * Reference to the FLC singleton
     *
     * @var    FLC
     */
    private static FLC $_instance;


    /**
     * The default db driver instance for the framework, will
     * be the default group defined in the database config.
     *
     * @var flcDriver
     */
    public flcDriver $db;

    /**
     * The language manager.
     *
     * @var flcLanguage
     */
    public flcLanguage $lang;

    /**
     * @var flcRequest The request manager
     */
    public flcRequest $request;

    /**
     * @var flcResponse The response manager
     */
    public flcResponse $output;

    /**
     * The validation manager
     *
     * @var flcValidation
     */
    public flcValidation $validation;


    /**
     * The rules for apply in validations
     *
     * @var array
     */
    private array $_vrules = [];

    /**
     * @var bool
     */
    private bool $_is_initialized = false;

    // --------------------------------------------------------------------


    /**
     * Class constructor, protected because to construct is required to
     * call get_instance() , because act as a singelton.
     *
     * @return    void
     * @throws Exception if initialize fail.
     */
    protected function __construct() {
        self::$_instance =& $this;
        $this->initialize();

    }

    // --------------------------------------------------------------------

    /**
     * Return the singletoin instance, previously initialized.
     *
     * @return FLC
     */
    public static function &get_instance(): FLC {
        static $loaded = false;
        if (!$loaded) {
            new FLC();
            $loaded = true;
        }

        return self::$_instance;
    }

    // --------------------------------------------------------------------

    /**
     * Initialize the class.
     * Check for extension mb_string and iconv and set the required globals.
     * Load the main config file.
     *
     * @return bool
     * @throws Exception when trying to load the main config class fails.
     * @used-by FLC::__construct
     */
    public function initialize(): bool {

        // Only can be initialized one time
        if (!$this->_is_initialized) {
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
                flcCommon::log_message('error', 'FLC->initialize - mb_string or iconv extensions are not found');

                throw new RuntimeException('This library rqquire the mb_string and iconv extensions - FLC0001');
            }

            // load the config manager class
            flcCommon::load_config();
        }

        return true;


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
     * @throws Exception
     * @see flcCommon::load_validation_config()
     */
    public function set_validations(string $p_filename, bool $p_reset = true): bool {

        $validations = flcCommon::load_validation_config($p_filename);
        // if no exception , continue.
        $nelems = count($validations);

        if ($nelems == 0) {
            flcCommon::log_message('debug', 'FLC->set_validations : No an array found as validations ');

            // not a terminal error
            return false;
        }

        // if reset , clear the array
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
            // grouped validations
            if (!$p_reset) {
                $this->_vrules = array_merge($this->_vrules, $validations);
            } else {
                $this->_vrules = $validations;
            }

        }


        return true;
    }

    // --------------------------------------------------------------------

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

    // --------------------------------------------------------------------


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

    // --------------------------------------------------------------------


    /**
     * return a view, for simplify code.
     * Search under APPPATH/views
     *
     * @param string $p_view the view name , can be something like '/special/menu',
     * it will load the view in APPPATH/views/special/menu.php.
     * @param mixed  $p_vars an object or array of variables.
     *
     * @return void
     * @throws Exception
     */
    public function view(string $p_view, $p_vars) {
        flcServiceLocator::get_instance()->service('views', $p_view, $p_vars);
    }

    // --------------------------------------------------------------------

    /**
     * The bootstrap for each request, load the requirements to execute
     * the controller.
     *
     * @return void
     * @throws Exception multiple . check error code
     */
    public function execute_request() {

        // charset stuff
        $charset = strtoupper(flcCommon::get_config()->item('charset'));
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

        // The timezone
        date_default_timezone_set(flcCommon::get_config()->item('timezone'));


        // Initialize the logger and clean the log directory
        flcServiceLocator::get_instance()->service('log')->do_log_rotate();

        // To setup constant UTF8_ENABLED
        flcUtf8::initialize();

        // The global language for the request.
        $this->lang = new flcLanguage();

        // database
        $this->db = flcServiceLocator::get_instance()->service('database');

        // get controller class name from uri.
        $uri = !flcCommon::is_cli() ? (flcCommon::is_https() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'] : '';
        $controller_class = flcCommon::uri_get_controller($uri);

        // load the output manager
        $this->output = new flcResponse();

        // load the request
        $this->request = new flcRequest();

        // create controller and initialize.
        $controller = flcServiceLocator::get_instance()->service('controller', $controller_class);
        $controller->initialize();
        $controller->pre_index();

        $controller->index();

        $controller->post_index();

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
    // --------------------------------------------------------------------

}