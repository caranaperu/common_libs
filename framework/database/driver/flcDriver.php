<?php

namespace framework\database\driver;

use framework\database\flcConnection;
use framework\database\flcDbResult;

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
abstract class flcDriver {
    /**
     * @var string
     */
    private string $version;

    /**
     * @var string
     */
    private $dbId;

    /**
     * Database driver identification, by default postgre
     *
     * @var    string
     */
    public $dbdriver = 'postgres';

    /**
     * Conteniendo los datos a la coneccion que maneja la instancia del driver
     * @var flcConnection
     */
    protected flcConnection $conn;

    public function __construct(flcConnection $conn) {
        $this->conn = $conn;
    }

    /**
     * Return the version of the database
     *
     * @return string with the version
     */
    public function getVersion(): ?string {
        return $this->version;
    }

    /**
     * Return the database id, valid values are :
     * mysql,postgres,mssql
     *
     * @return string the database id
     */
    public function getDbId(): ?string {
        return $this->dbId;
    }

    public function get_connection(): flcConnection {
        return $this->conn;
    }


    /**
     *
     * Connect to a database using the data in flcConnection.
     * Previously flcConnection $conn nee to be initialized
     *
     * @return bool true if is connected , false if not.
     */
    public abstract function connect(): bool;

    /**
     *
     * Disconnect from a database using the data in flcConnection.
     * Previously flcConnection $conn need to be initialized
     *
     * @return void
     */
    public abstract function disconnect(): void;

    /**
     * Some db servers require select an specific database before use in a query
     * those need to do a valid implementation of tihis method , others return true
     * is enough.
     *
     * @param string $database_name the database name
     *
     * @return bool true if selected
     */
    public abstract function select_database(string $database_name): bool;


    /**
     * Execute the query in low level , the return is a resource that depends of each
     * database that contain the answer.
     *
     * @param string $sql an SQL query
     *
     * @return    resource|bool
     */
    protected abstract function _execute(string $sql);


    /**
     * @param string $sqlqry
     *
     * @return flcDbResult|null
     */
    public function execute_query(string $sqlqry) : ?flcDbResult {
        if (trim($sqlqry) === '') {
            return null;
        }

        if (false === ($result_id = $this->_execute($sqlqry))) {
            return null;
        } else {
            // Load and instantiate the result driver
            $driver = $this->load_result_driver();
            $driver = 'framework\database\driver\\'.$this->dbdriver.'\\'.$driver;
            return new $driver($this,$result_id);
        }
    }


    // --------------------------------------------------------------------

    /**
     * Load the result drivers
     *
     * @return    string    the name of the result class
     */
    public function load_result_driver(): string {
        $driver = 'flc'.ucwords($this->dbdriver).'Result';

        if (!class_exists($driver, FALSE)) {
            require_once('flcDbResult.php');
            require_once('./driver/'.$this->dbdriver.'/'.$driver.'.php');
        }

        return $driver;
    }

}
