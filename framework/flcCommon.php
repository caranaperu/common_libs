<?php

namespace framework;

use framework\database\driver\flcDriver;

/**
 * FLabsCode
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2022 - 2022, Future Labs Corp-
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    FLabsCode
 * @author    Carlos Arana
 * @copyright    Copyright (c) 2022 - 2022, FLabsCode
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://flabscorpprods.com
 * @since    Version 1.0.0
 * @filesource
 */
class flcCommon {

    /**
     * Load the config file that need to be in the APPPATH defined and
     * config directory.
     * if fails exit , without config is not possible to continue.
     *
     * @return array with the config options
     */
    static function &load_config(): array {
        static $config;

        // only load one time
        if (!isset($config) || !$config) {
            if (file_exists(APPPATH.'config/config.php')) {
                // load config file
                require APPPATH.'config/config.php';

                //  check if the config values are ok.
                if (!isset($config) || !$config || !is_array($config)) {
                    self::exit_error('config() : error loading the config file bad configuration', 503);
                }

            } else {
                self::exit_error('config() : error config file doesnt exist in '.APPPATH.'config', 503);
            }
        }

        return $config;

    }

    static function &get_config(): array {
        return self::load_config();

    }

    /**
     * Load the database config file that need to be in the APPPATH defined and
     * config directory.
     * if fails exit , without config is not possible to continue.
     *
     * @return array with the databaseconfig options.
     */
    static function &load_database_config(): array {
        static $db;

        // only load one time
        if (!isset($db) || !$db) {
            echo 'loading .....'.PHP_EOL;
            if (file_exists(APPPATH.'config/database.php')) {
                require_once APPPATH.'config/database.php';

                //  check if the config values are ok.
                if (!isset($db) || !$db || !is_array($db)) {
                    self::exit_error('load_database_config() : error loading the database config file, bad configuration', 503);
                } else {
                    // Check if active group entry exist ($active_group is in the db config file loaded)
                    if (isset($active_group) && isset($db[$active_group])) {

                        // add to the db config indicate by $active_group the active flag
                        $db[$active_group]['active'] = true;
                    } else {
                        self::exit_error('load_database_config() : the active group variable not found or not entry on the db array for the active group defined', 503);
                    }
                }
            } else {
                self::exit_error('load_database_config() : error database config file doesnt exist in '.APPPATH.'config', 503);
            }
        }

        return $db;
    }

    /**
     * Load the validation config file that need to be in the APPPATH defined and
     * config directory.
     * The validation file will be searched on the config subdirectory 'validation'.
     *
     * @return array of rules or group of rules or null if file doesnt exist.
     */
    static function load_validation_config(string $p_filename): ?array {

        // only load one time
        echo 'loading validation .....'.PHP_EOL;
        $path = APPPATH."config/validation/$p_filename.php";
        if (file_exists($path)) {
            require_once $path;
            return $config;
        }
        flcCommon::log_message('warning', "Validation file $p_filename doesnt exist");

        return null;
    }

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
     */
    static function load_database(?string $p_dbid = null): flcDriver {
        $db = self::load_database_config();

        // If required a specific database entry
        if ($p_dbid) {
            if (isset($db[$p_dbid])) {
                if (isset($db[$p_dbid]['dbdriver'])) {
                    $actgroup = $p_dbid;
                    $drvname = $db[$p_dbid]['dbdriver'];

                } else {
                    self::exit_error("load_database() : Database config file doesnt have a 'driver' index", 503);
                }
            } else {
                self::exit_error("load_database()): Database identified by $p_dbid is not on the database config file", 503);
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
                self::exit_error("load_database() : Cant find an entry for the active group defined", 503);

            }
        }


        // load driver an initialize
        if (isset($drvname) && strlen(trim($drvname)) > 0) {
            $driver = 'flc'.ucwords($drvname).'Driver';
            $driver_class = 'framework\database\driver\\'.$drvname.'\\'.$driver;

            if (!class_exists($driver_class, false)) {
                echo 'Load class '.$driver.PHP_EOL;

                require_once dirname(__FILE__)."/database/driver/flcDriver.php";
                require_once dirname(__FILE__)."/database/driver/$drvname/$driver.php";
            }

            // Can have multiple instances if required
            // create with options
            $drv = new $driver_class($db[$actgroup]);


            // initialize
            if (!$drv->initialize($db[$actgroup]['dsn'] ?? null, $db[$actgroup]['hostname'], $db[$actgroup]['port'] ?? null, $db[$actgroup]['database'], $db[$actgroup]['username'], $db[$actgroup]['password'], $db[$actgroup]['char_set'], $db[$actgroup]['dbcollat'])) {
                self::exit_error("Cant initialize the db group $group because some elements are bad defined", 503);
            }

            // Open a new connection (can have multiples, but only  one is the default)
            if (!$drv->connect()) {
                $error = $drv->error();
                self::exit_error("Cant connect the db group '$actgroup' , verify database config, cause = {$error['message']}", 503);
            }


            return $drv;


        } else {
            self::exit_error("Database identified by '$actgroup' doesnt have a driver specified", 503);

        }

    }

    /**
     * @param string $p_errormsg
     * @param int    $p_http_error
     *
     * @return void
     */
    static function exit_error(string $p_errormsg, int $p_http_error = -1) {
        if ($p_http_error != -1) {
            // setera el header error
        }
        if (strlen($p_errormsg) > 0) {
            echo $p_errormsg;
        } else {
            echo 'unknow_error';
        }
        exit(3);

    }

    static function log_message(string $p_type, string $p_message) {
        echo $p_type.' : '.$p_message;
    }


}