<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Inspired in codeigniter , all kudos for his authors
 *
 * @author Carlos Arana Reategui.
 *
 */
namespace flc\core;

use Exception;
use flc\flcCommon;


/**
 * Config manager class.
 *
 * Allow to load the different config files in the APPPATH/config directory or in the
 * directories under config defined by the ENVIRONMENT variable , for debug purposes
 * for example.
 *
 */
class flcConfig {
    /**
     * List of all loaded config values
     *
     * @var    array
     */
    protected array $_config = [];

    /**
     * List of all loaded config files
     *
     * @var    array
     */
    protected array $_is_loaded = [];

    /**
     * List of paths to search when trying to load a config file.
     *
     * @var        array
     */
    protected array $_config_paths = [APPPATH];

    /**
     * Load the main config file that need to be in the APPPATH defined and
     * config directory.
     * if fails exit , without config is not possible to continue.
     *
     * @return array with the config options
     * @throws Exception
     */
    public function load_config(): ?array {
        // load method ,by default load the main config file
        if (!$this->load()) {
            flcCommon::log_message('error', 'flcConfig->load_config - error config file doesnt exist ');
            return null;
        }

        return $this->_config;

    }

    // --------------------------------------------------------------------

    /**
     * Load Config Files
     *
     * @param string $file Configuration file name
     * @param bool   $use_sections Whether configuration values should be loaded into their own section
     *
     * @return    bool    true if the file was loaded correctly or false on failure
     * @throws Exception
     */
    public function load(string $file = '', bool $use_sections = false): bool {
        // if not defined load the main config class otherwise take the file and remove php extesion
        $file = ($file === '') ? 'config' : str_replace('.php', '', $file);

        $loaded = false;
        $env = ENVIRONMENT;
        $locations = [$file];

        // if ENVIROMENT is development use confg subdirectory as alternativa, otherwise use the APPPATH/config subdirectory
        if (ENVIRONMENT == 'development') {
            // add to locations the subdirectory as a possible location.
            $locations[] = ENVIRONMENT.DIRECTORY_SEPARATOR.$file;
        }

        foreach ($this->_config_paths as $path) {
            foreach ($locations as $location) {
                $file_path = $path.'config/'.$location.'.php';
                // load only one time.
                if (in_array($file_path, $this->_is_loaded, true)) {
                    return true;
                }

                if (!file_exists($file_path)) {
                    continue;
                }

                include($file_path);

                // check if the array config exist
                if (!isset($config) or !is_array($config)) {
                    return false;
                }

                if ($use_sections === true) {
                    $this->_config[$file] = isset($this->_config[$file]) ? array_merge($this->_config[$file], $config) : $config;
                } else {
                    $this->_config = array_merge($this->_config, $config);
                }

                $this->_is_loaded[] = $file_path;
                $config = null;
                $loaded = true;
                flcCommon::log_message('debug', "flcConfig->load : Config file loaded ".$file_path);

            }
        }

        return $loaded;

    }


    // --------------------------------------------------------------------

    /**
     * Fetch a config file item
     *
     * @param string $p_item Config item name, if p_group is '' theni s the main
     * index , otherwise o_group will be the main and this parameter the secondary
     * index.
     * @param string $p_group group name if the config tio get is on a gropu.
     *
     * @return    string|null    The configuration item or NULL if the item doesn't exist
     */
    public function item(string $p_item, string $p_group = '') : ?string{
        if ($p_group == '') {
            return $this->_config[$p_item] ?? null;
        }

        return isset($this->_config[$p_group], $this->_config[$p_group][$p_item]) ? $this->_config[$p_group][$p_item] : null;
    }
}