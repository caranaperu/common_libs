<?php

namespace framework\core;

use Exception;
use framework\flcCommon;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use RuntimeException;

class flcServiceLocator {
    private static flcServiceLocator $_instance;

    public static function &get_instance(): flcServiceLocator {
        static $loaded = false;
        if (!$loaded) {
            new flcServiceLocator();
            $loaded = true;
        }

        return self::$_instance;
    }

    private function __construct() {
        self::$_instance = &$this;
    }

    /**
     * @param string      $p_type
     * @param string|null $p_class
     * @param null        $p_vars
     *
     * @return Object|bool
     * @throws ReflectionException
     * @throws Exception
     */
    public function service(string $p_type, ?string $p_class = null, $p_vars = null) {
        $ret = false;
        switch ($p_type) {

            case 'controller':
                $ret = $this->_get_controller($p_class);
                break;

            case 'views':
                $this->_get_view($p_class, $p_vars);
                $ret = true;
        }

        return $ret;
    }

    /**
     * Load the base controller and the user controller if its required.
     *
     * @param string|null $p_class if its needed to load and create and specific user controller
     * this params set the class name. if its null only load the base controller and the exteneded
     * controller if exist.
     *
     * @return flcIController|null if $p_class not defined return null otherwise return the class.
     *
     * @throws ReflectionException
     * @throws Exception
     */
    private function _get_controller(?string $p_class = null): ?flcIController {
        $config = FLC::get_instance()->get_config();

        if ($p_class) {
            $controller_user_file = APPPATH."controllers/$p_class.php";
            if (!file_exists($controller_user_file)) {
                throw new RuntimeException("The controller [ $p_class ] doesnt exist, terminal error (SL0001)");
            }

        }

        // always include the base standard controller
        $controller_base_file = BASEPATH."/core/flcController.php";
        if (file_exists($controller_base_file)) {
            include_once $controller_base_file;

        } else {
            throw new RuntimeException("Base controller [ $p_class ] doesnt exist, check deployment of library (SL0002)");
        }


        // first check if the user overload the class with extension
        $controller_base_file = APPPATH."/flc/core/flcControllerExt.php";
        if (file_exists($controller_base_file)) {
            include_once $controller_base_file;

            $ns = $config->item('controller_ext', 'namespaces');
            if (isset($ns)) {
                // not is blank , proceed , blank means no controller extended will be used.
                if (trim($ns) != '') {
                    $ns_class = "$ns\\flcControllerExt";

                    // Class verification
                    if (!class_exists($ns_class, false)) {
                        throw new RuntimeException("Cant load the extended controler [ $ns_class ], terminal error (SL00003)");
                    }

                    $reflection = new ReflectionClass($ns_class);
                    if (!$reflection->isSubclassOf('framework\core\flcController')) {
                        throw new RuntimeException("controller [ $ns_class ] need to be an instance of framework\core\\flcController (SL00008)");
                    }
                }

            } else {
                // at least '' is requLired in the config.
                // 'controller_ext' => ''
                throw new RuntimeException("config entry for namespaces/controller_ext is not defined or bad defined (SL00006)");
            }

        }

        // if name is  defined means load and create an instance of the user class .
        if ($p_class) {
            $ns = $config->item('controllers', 'namespaces');
            if (isset($ns) && trim($ns) !== '') {

                include_once $controller_user_file;
                $ns_class = "$ns\\$p_class";

                // class validation
                if (!class_exists($ns_class, false)) {
                    throw new RuntimeException("Cant load the controler [ $ns_class ], terminal error (SL00003)");

                } elseif (!method_exists($ns_class, 'index')) {
                    throw new RuntimeException("Cant load the controler [ $ns_class ] , index method doesnt exist, terminal error (SL00004)");
                } elseif (!is_callable([$p_class, 'index'])) {
                    $reflection = new ReflectionMethod($ns_class, 'index');
                    if (!$reflection->isPublic()) {
                        throw new RuntimeException("The index method of [ $ns_class ] need to be public, terminal error (SL00005)");
                    }

                }

                $reflection = new ReflectionClass($ns_class);
                if ($reflection->isSubclassOf('framework\core\flcController')) {
                    return new $ns_class;

                } else {
                    throw new RuntimeException("controller [ $ns_class ] need to be an instance of framework\core\\flcController (SL00007)");

                }

            } else {
                throw new RuntimeException("config entry for namespaces/controllers is not defined or bad defined (SL00006)");
            }
        }

        return null;
    }

    /**
     * Load the base controller and the user controller if its required.
     *
     * @param string|null $p_class if its needed to load and create and specific user controller
     * this params set the class name. if its null only load the base controller and the exteneded
     * controller if exist.
     *
     * @throws Exception
     */
    private function _get_view(string $p_class, $p_vars = null) {
        static $ob_level = -1;

        if ($ob_level === -1) {
            $ob_level = ob_get_level();
        }

        $file_exist = false;

        $_ci_ext = pathinfo($p_class, PATHINFO_EXTENSION);
        $_ci_file = ($_ci_ext === '') ? $p_class.'.php' : $p_class;

        // load the views
        $view_filepath = VIEWPATH.$_ci_file;

        if (file_exists($view_filepath)) {
            flcCommon::log_message('info',"flcServiceLocator->_get_view - view in $view_filepath is loaded");
        } else {
            throw new RuntimeException("Cant load the views $view_filepath");
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

        $vars = $p_vars;
        if (!is_array($p_vars)) {
            $vars = is_object($p_vars) ? get_object_vars($p_vars) : [];
        }

        foreach ($vars as $key => $value) {
            $$key = $value;
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
            FLC::get_instance()->output->append_output(ob_get_contents());
            @ob_end_clean();
        }


    }
}