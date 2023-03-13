<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 **
 * @author Carlos Arana Reategui.
 *
 */

namespace flc\core;

use Exception;
use flc\core\accessor\flcDbAccessor;
use flc\core\session\flcSession;
use flc\database\driver\flcDriver;
use flc\flcCommon;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;
use Throwable;


/**
 * Service locator class.
 * Singleton to load instances of several common services like , controllers , views and databases.
 *
 * @history 14-02-2022  _get_db_accessor added
 */
class flcServiceLocator {
    private static flcServiceLocator $_instance;

    /**
     *Private constructor for singleton purposes
     */
    private function __construct() {
        self::$_instance = &$this;
    }

    // --------------------------------------------------------------------

    /**
     * Get the singleton instance of the class.
     *
     * @return flcServiceLocator
     */
    public static function &get_instance(): flcServiceLocator {
        static $loaded = false;
        if (!$loaded) {
            new flcServiceLocator();
            $loaded = true;
        }

        return self::$_instance;
    }

    // --------------------------------------------------------------------


    /**
     *
     * @param string      $p_type
     * @param string|null $p_service_id the controller name ,  view name or database group
     * @param mixed|null  $p_vars variables to pass to the service if required.
     *
     * @return Object|bool the service instance (for views only return a boolean).
     *
     * @throws Throwable
     */
    public function service(string $p_type, ?string $p_service_id = null, $p_vars = null) {
        $ret = false;
        switch ($p_type) {

            case 'controller':
                $ret = $this->_get_controller($p_service_id);
                break;

            case 'views':
                $this->_get_view($p_service_id, $p_vars);
                $ret = true;
                break;

            case 'database':
                $ret = $this->_load_database($p_service_id);
                break;

            case 'log':
                $ret = $this->_load_log($p_vars);
                break;

            case 'session':
                $ret = $this->_get_session($p_vars);
                break;

            case 'accessor':
                $ret = $this->_get_db_accessor($p_service_id, $p_vars);
        }

        return $ret;
    }

    // --------------------------------------------------------------------

    /**
     * Load the base controller and the user controller if its required.
     * Important: Is required in config file to define :
     *      $config['namespaces'] = [
     *              'controllers' => 'a namespace'
     *              ];
     *
     * @param string $p_class its needed to load and create and specific user controller
     * this params set the class name.
     *
     * @return flcController|null if $p_class not defined return null otherwise return the class.
     *
     * @throws Throwable
     */
    private function _get_controller(string $p_class): ?flcController {
        $config = flcCommon::get_config();

        // Check if the file exist (allways need to be in the APPPATH controllers subdirectory)
        $controller_user_file = APPPATH."controllers/$p_class.php";
        if (!file_exists($controller_user_file)) {
            throw new RuntimeException("The controller [ $p_class ] doesnt exist, terminal error - SL0001");
        }


        // always include the base standard controller
        $controller_base_file = BASEPATH."/core/flcController.php";
        if (file_exists($controller_base_file)) {
            include_once $controller_base_file;

        } else {
            throw new RuntimeException("Base controller [ $controller_base_file ] doesnt exist, check deployment of library - SL0002");
        }


        //  load and create an instance of the user class (with verify)
        $ns = $config->item('controllers', 'namespaces');
        if (isset($ns) && trim($ns) !== '') {

            include_once $controller_user_file;
            $ns_class = "$ns\\$p_class";

            // class validation
            if (!class_exists($ns_class, false)) {
                throw new RuntimeException("Cant load the controler [ $ns_class ] - SL00009");

            } elseif (!method_exists($ns_class, 'index')) {
                throw new RuntimeException("Cant load the controler [ $ns_class ] , index method doesnt exist - SL00004");
            } elseif (!is_callable([$p_class, 'index'])) {
                try {
                    $reflection = new ReflectionMethod($ns_class, 'index');
                    if (!$reflection->isPublic()) {
                        throw new RuntimeException("The index method of [ $ns_class ] need to be public - SL00005");
                    }

                } catch (ReflectionException $ex) {
                    throw new RuntimeException("The index method of [ $ns_class ] doesnt exist - SL00022");

                }

            }

            try {
                $reflection = new ReflectionClass($ns_class);
                if ($reflection->isSubclassOf('flc\core\flcController')) {
                    return new $ns_class;

                } else {
                    throw new RuntimeException("controller [ $ns_class ] need to be an instance of flc\core\\flcController - SL00007");

                }

            } catch (ReflectionException $ex) {
                throw new RuntimeException("controller [ $ns_class ] doesnt exist - SL00021");

            }

        } else {
            throw new RuntimeException("config entry for namespaces/controllers is not defined or bad defined - SL00006");
        }

    }

    // --------------------------------------------------------------------

    /**
     * Load the view identified by p_view_name , this will be the base name of the file
     * that contains the view can include an extension , without the extension php will be used
     *
     * @param string|null $p_view_name the view file name , will be searhed on APPPATH/views.
     * @param mixed|null  $p_vars the variables to pass to the view.
     *
     * @throws RuntimeException
     */
    private function _get_view(string $p_view_name, $p_vars = null) {
        static $ob_level = -1;

        if ($ob_level === -1) {
            $ob_level = ob_get_level();
        }

        $file_exist = false;

        // get the full file name with extension , php its not indicated.
        $_ci_ext = pathinfo($p_view_name, PATHINFO_EXTENSION);
        $_ci_file = ($_ci_ext === '') ? $p_view_name.'.php' : $p_view_name;

        // load the views
        $view_filepath = VIEWPATH.$_ci_file;

        if (!file_exists($view_filepath)) {
            throw new RuntimeException("Cant load the views $view_filepath - SL0010");
        }

        /*
         * Buffer the output
         *
         * We buffer the output for two reasons:
         * 1. Speed. You get a significant speed boost.
         * 2. So that the final rendered template can be post-processed by
         *	the output class or controller class.
         */
        ob_start();

        // convert the params from array or object to flat variables  to pass to the view.
        $vars = $p_vars;
        if (!is_array($p_vars)) {
            // Object processing
            $vars = is_object($p_vars) ? get_object_vars($p_vars) : [];
        } else {
            // array processing
            foreach ($vars as $key => $value) {
                $$key = $value;
            }
        }

        include($view_filepath); // include() vs include_once() allows for multiple views with the same name


        /*
         * Flush the buffer... or buff the flusher?
         *
         * In order to permit views to be nested within
         * other views, we need to flush the content back out whenever
         * we are beyond the first level of output buffering so that
         * it can be seen and included properly by the first included
         * template and any subsequent ones. Oy!
         */
        if (ob_get_level() > $ob_level + 1) {
            ob_end_flush();
        } else {
            flcResponse::get_instance()->append_output(ob_get_contents());
            @ob_end_clean();
        }


    }
    /**********************************************************************
     * DATABASE stuff
     */


    /**
     * Load the database config file that need to be in the APPPATH defined and
     * config directory.
     * if fails throws an exception.
     *
     * @return array with the database config options.
     *
     * @throws RuntimeException
     */
    private function &_load_database_config(): array {
        static $db;

        // only load one time
        if (!isset($db) || !$db) {
            if (file_exists(APPPATH.'config/database.php')) {
                require_once APPPATH.'config/database.php';

                //  check if the config values are ok.
                if (!isset($db) || !$db || !is_array($db)) {
                    throw new RuntimeException("Error loading the database config file, bad configuration - SL0011");

                } else {
                    // Check if active group entry exist ($active_group is in the db config file loaded)
                    if (isset($active_group) && isset($db[$active_group])) {

                        // add to the db config indicate by $active_group the active flag
                        $db[$active_group]['active'] = true;
                    } else {
                        throw new RuntimeException("the active group variable not found or not entry on the db array for the active group defined [ isset($active_group) ? $active_group : '' ] - SL0012");
                    }
                }
            } else {
                throw new RuntimeException('Error database config file doesnt exist in '.APPPATH.' config - SL0013');
            }
        }

        return $db;
    }

    // --------------------------------------------------------------------

    /**
     * Load a specific database based on the group identifier $p_dbid.
     * After that load classes , initialize and connect to the database.
     *
     * Always return a new connection , if called many times with the same
     * p_dbid a new instance and connection will be created.
     *
     * @param string|null $p_dbid if null load the active one.
     *
     * @return flcDriver
     * @throws RuntimeException
     */
    private function _load_database(?string $p_dbid = null): flcDriver {
        $db = $this->_load_database_config();

        // If required a specific database entry
        if ($p_dbid !== null) {
            if (isset($db[$p_dbid])) {
                if (isset($db[$p_dbid]['dbdriver'])) {
                    $actgroup = $p_dbid;
                    $drvname = $db[$p_dbid]['dbdriver'];

                } else {
                    throw new RuntimeException("Database config file doesnt have a driver index- COM0007");
                }
            } else {
                throw new RuntimeException("Database identified by [ $p_dbid ] is not on the database config file - SL0014");
            }
        } else {
            // Otherwise find the active group.
            foreach ($db as $group => $value) {
                if (isset($value['active']) && $value['active']) {
                    $actgroup = $group;
                    $drvname = $value['dbdriver'];
                    break;
                }
            }

            if (!isset($actgroup)) {
                throw new RuntimeException("Cant find an entry for the active database  group defined - SL0015");
            }
        }


        // load driver an initialize
        if (isset($drvname) && strlen(trim($drvname)) > 0) {
            $driver = 'flc'.ucwords($drvname).'Driver';
            $driver_class = 'flc\database\driver\\'.$drvname.'\\'.$driver;

            if (!class_exists($driver_class, false)) {

                require_once dirname(__FILE__)."/../database/driver/flcDriver.php";
                require_once dirname(__FILE__)."/../database/driver/$drvname/$driver.php";
            }

            // Can have multiple instances if required
            // create with options
            $drv = new $driver_class($db[$actgroup]);


            // initialize
            if (!$drv->initialize($db[$actgroup]['dsn'] ?? null, $db[$actgroup]['hostname'], $db[$actgroup]['port'] ?? null, $db[$actgroup]['database'], $db[$actgroup]['username'], $db[$actgroup]['password'], $db[$actgroup]['char_set'], $db[$actgroup]['dbcollat'])) {
                throw new RuntimeException("Cant initialize db group [ $actgroup ] because some elements are bad defined - SL0016");

            }

            // Open a new connection (can have multiples, but only  one is the default)
            if (!$drv->connect()) {
                $error = $drv->error();
                throw new RuntimeException("Cant connect the db group [ $actgroup ] , verify database config, cause = {$error['message']} - SL0017");
            }


            return $drv;


        } else {
            throw new RuntimeException("Database identified by [ $actgroup ] doesnt have a driver specified - SL0018");

        }

    }

    // --------------------------------------------------------------------

    /**
     * Load the log config file that need to be in the APPPATH defined and
     * config directory. This will be called one time by _load_log
     * if fails throws an exception.
     *
     * @return array with the log config options.
     * @throws RuntimeException
     * @see flcServiceLocator::_load_log()
     */
    private function &_load_log_config(): array {

        // only load one time
        if (file_exists(APPPATH.'config/log.php')) {
            require_once APPPATH.'config/log.php';

            //  check if the log values are ok.
            if (!isset($log_config) || !$log_config || !is_array($log_config)) {
                throw new RuntimeException("Error loading the log config file, bad configuration - SL0019");

            }
        } else {
            throw new RuntimeException('Error log config file doesnt exist in '.APPPATH.' log - SL0020');
        }

        return $log_config;
    }

    // --------------------------------------------------------------------

    /**
     * Load load config and create the class, return the unique instance.
     * If $p_log_config is null load the config from APPPATH/config/log.php
     *
     * @param array|null $p_log_config config options or null
     *
     * @return flcLog
     * @throws RuntimeException
     */
    private function _load_log(?array $p_log_config): flcLog {
        static $log;

        if (!isset($log) || !$log) {
            if ($p_log_config !== null) {
                $log_config = $p_log_config;
            } else {
                $log_config = $this->_load_log_config();
            }

            $log = new flcLog($log_config);
        }

        return $log;
    }
    // --------------------------------------------------------------------


    /**
     * @throws Exception
     */
    private function _get_session($p_ip_address): flcSession {
        static $session;

        $user_handler = false;
        if (!isset($session) || !$session) {
            $config = flcCommon::get_config();
            $handler = $config->item('sess_driver');


            // always include the base standard controller
            $handler_base_file = BASEPATH."/core/session/handler/flcBaseHandler.php";
            if (file_exists($handler_base_file)) {
                include_once $handler_base_file;
            } else {
                throw new RuntimeException("Base session handler doesnt exist, check deployment of library - SL00024");
            }


            if (isset($handler) && !empty($handler)) {
                $handler_filepath = APPPATH."handlers/$handler.php";
                if (file_exists($handler_filepath)) {
                    $user_handler = true;
                }

            } else {
                // default is file handler
                $handler = 'file';
            }

            if (!$user_handler) {
                // try on our own lib directory
                $handler_driver = 'flc'.ucwords($handler).'Handler';
                $handler_filepath = BASEPATH."/core/session/handler/$handler_driver.php";

                if (file_exists($handler_filepath)) {
                    include_once $handler_filepath;
                    $handler_driver = "\\flc\core\session\handler\\$handler_driver";

                    $handler_instance = new $handler_driver($p_ip_address);

                    $session = new flcSession($handler_instance, $config);
                } else {
                    throw new RuntimeException("Session handler [ $handler_driver ] doesnt exist, check deployment of library - SL00025");
                }


            } else {
                // validate user handler
                $ns = $config->item('handlers', 'namespaces');
                if (isset($ns) && trim($ns) !== '') {

                    include_once $handler_filepath;
                    $ns_class = "$ns\\$handler";

                    // class validation
                    if (!class_exists($ns_class, false)) {
                        throw new RuntimeException("Cant load the user session handler [ $ns_class ] - SL00026");

                    }

                    try {
                        $reflection = new ReflectionClass($ns_class);
                        if ($reflection->isSubclassOf('flc\core\session\handler\flcBaseHandler')) {
                            $handler_instance = new $ns_class($p_ip_address);

                            $session = new flcSession($handler_instance, $config);

                        } else {
                            throw new RuntimeException("User session handler [ $ns_class ] need to be an instance of flc\core\session\handler\\flcBaseHandler - SL00027");

                        }

                    } catch (ReflectionException $ex) {
                        throw new RuntimeException("User session handler [ $ns_class ] doesnt exist - SL00028");

                    }


                } else {
                    throw new RuntimeException("config entry for namespaces/handlers is not defined or bad defined - SL00029");
                }
            }

        }

        if (session_status() === PHP_SESSION_NONE) {
            $session->start();
        }

        return $session;
    }

    /*--------------------------------------------------------------*/


    /**
     * Load a derived accessor from flcDbAccessor, checking first if a specific one exists
     * for the indicated driver's database.
     *
     * If one is used for a particular database, it should have the format accessorname_dbid,
     * for example invoice_controller_mssql where mssql corresponds to the database indicator
     * obtained from the driver through the get_db_driver() method.
     *
     * If the specific one does not exist, it will try to load the normal one.
     *
     * If the accessor class doesn't directly derive from flcDbAccessor, it should have the
     *  corresponding include or use, or otherwise it should exist in the class autoload.
     *
     * @param string    $p_accessor_class The base class name of the accessor without
     * indicating the database ID , BUT WITH THE NAMESPACE SPECIFIED!!!
     *
     * @param flcDriver $p_driver db driver instance (constructor parameter of an accessor)
     *
     * @return flcDbAccessor the instance class
     * @throws Exception
     */
    private function _get_db_accessor(string $p_accessor_class, flcDriver $p_driver): flcDbAccessor {
        // what database ?
        $db_driverid = $p_driver->get_db_driver();

        //$config = flcCommon::get_config();

        $parts = explode('\\',$p_accessor_class);

        // Check if the specific db accessor file exist (allways need to be in the APPPATH controllers subdirectory)
        $accessor_class = $p_accessor_class.'_'.$db_driverid;
        $accessor_file = APPPATH.'accessors/'.end($parts).'-'.$db_driverid.'.php';
        if (!file_exists($accessor_file)) {
            // if not exist search the file
            $accessor_class = $p_accessor_class;
            $accessor_file = APPPATH.'accessors/'.end($parts).'.php';
            if (!file_exists($accessor_file)) {
                throw new RuntimeException("The accessor [ $p_accessor_class ] doesnt exist, terminal error - SL0030");
            }
        }


        // always include the base standard controller
        $accessor_base_file = BASEPATH."/core/accessor/flcDbAccessor.php";
        if (file_exists($accessor_base_file)) {
            include_once $accessor_base_file;

        } else {
            throw new RuntimeException("Base accessor [ $accessor_base_file ] doesnt exist, check deployment of library - SL0031");
        }

        include_once $accessor_file;

        // class validation
        if (!class_exists($accessor_class, false)) {
            throw new RuntimeException("Cant load the accessor [ $accessor_class ] - SL00032");

        }

        try {
            $reflection = new ReflectionClass($accessor_class);
            if ($reflection->isSubclassOf('flc\core\accessor\flcDbAccessor')) {
                return new $accessor_class($p_driver);

            } else {
                throw new RuntimeException("accessor [ $accessor_class ] need to be an instance of flc\core\\accessor\\flcController - SL00033");

            }

        } catch (ReflectionException $ex) {
            throw new RuntimeException("accessor [ $accessor_class ] doesnt exist - SL00034");

        }

    }
}