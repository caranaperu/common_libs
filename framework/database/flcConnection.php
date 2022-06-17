<?php


namespace framework\database;

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

/**
 * Database Connection Class
 *
 * This is the platform-independent connection class.
 * This class need to be overloaded for specific databases..
 *
 * Important : This class contains part from codeigniter
 * all credits for his authors.
 *
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
abstract class flcConnection {
    /**
     * Represents an connection id , this a resource that depends on each database.
     * @var resource|null
     */
    protected $_connId;


    /**
     * Collation , basically for table creation.
     *
     * @var    string
     */
    protected string $_collation = 'utf8_general_ci';

    /**
     * charset , if defined used on open connection.
     *
     * @var    string
     */
    protected string $_charset = 'utf8';

    /**
     * @var string
     */
    protected string $_dsn;

    /**
     * Encryption flag/data
     *
     * @var    bool | array
     */
    public $encrypt = FALSE;


    /**
     *
     * initialize the data required for a connection using a dsn or separate parameters required for the connection.
     * IF dsn is incomplete the function will try to use the another parameters to complete
     * the required data.
     *
     * If no dsn is specified all others need to be.
     *
     * @param string | null $p_dsn Then dsn of the connection
     * @param string | null $p_host The hostname or ip address of the server.
     * @param int | null    $p_port The por number
     * @param string | null $p_database The database name to connect
     * @param string | null $p_user The user name
     * @param string | null $p_password The password
     * @param string        $p_charset The default charset to be used by the connection , default utf8
     * @param string        $p_collation The default collation for the connection , default utf8_general_ci
     *
     *
     * @return bool FALSE if not enough parameter data to create a correct dsn.
     */
    public abstract function initialize(?string $p_dsn, ?string $p_host, ?int $p_port, ?string $p_database, ?string $p_user, ?string $p_password, string $p_charset = 'utf8', string $p_collation = 'utf8_general_ci'): bool;

    // --------------------------------------------------------------------

    /**
     * Extract the host from the dsn , if dsn is not initialized or host
     * is not defined return null.
     *
     * @return string|null The host value
     */
    public function get_host(): ?string {
        if (isset($this->_dsn) && $this->_dsn) {
            return parse_url($this->_dsn, PHP_URL_HOST);
        }

        return null;
    }

    // --------------------------------------------------------------------

    /**
     * Extract the port from the dsn , if dsn is not initialized or port
     * is not defined return null.
     *
     * @return string|null The port value
     */
    public function get_port(): ?string {
        if (isset($this->_dsn) && $this->_dsn) {
            $port = parse_url($this->_dsn, PHP_URL_PORT);
            if ($port) {
                return strval($port);
            }
        }

        return null;
    }

    // --------------------------------------------------------------------

    /**
     * Extract the user name  from the dsn , if dsn is not initialized or user
     * is not defined return null.
     *
     * @return string|null The user name  value
     */
    public function get_user(): ?string {
        if (isset($this->_dsn) && $this->_dsn) {
            return parse_url($this->_dsn, PHP_URL_USER);
        }

        return null;
    }

    // --------------------------------------------------------------------

    /**
     * Extract the database name from the dsn , if dsn is not initialized or database
     * is not defined return null.
     * The database for a db dsn is on the path of the url.
     *
     * @return string|null The database name value
     */
    public function get_database(): ?string {
        if (isset($this->_dsn) && $this->_dsn) {
            $database = parse_url($this->_dsn, PHP_URL_PATH);
            // in database url the path is the database then remove the trailing
            // slash.
            if ($database !== null) {
                return str_replace('/', '', $database);
            }
        }

        return null;
    }

    // --------------------------------------------------------------------

    /**
     * Set the database name in the dsn , if dsn is not initialized or database
     * is not defined return false.
     * The database for a db dsn is on the path of the url.
     *
     * IMPORTANT: For internal use only.
     *
     * @param string $p_database the database name
     *
     * @return bool false if can set the database name
     */
    public function _set_database(string $p_database): bool {
        if (isset($this->_dsn) && $this->_dsn) {
            $database = '/'.$this->get_database();
            // in database url the path is the database
            if ($database !== '/') {
                str_replace($database, $p_database, $this->_dsn);
                return true;
            }
        }
        return false;
    }

    // --------------------------------------------------------------------

    /**
     * @return resource|null
     */
    public function get_connection_id() {
        return $this->_connId;
    }

    // --------------------------------------------------------------------

    /**
     * Open the connection used by this instance, if is already
     * a live connection the current one will be used, if this is not
     * the desired action call close method first.
     *
     * @return bool true if can get connection or already one open , false otherwise
     */
    public function open(): bool {
        if (!isset($this->_connId) || !$this->_connId) {

            $conn = $this->_open();

            if (!$conn) {
                return false;
            }
            $this->_connId = $conn;
        }


        $this->_set_charset($this->_charset);

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Close the connection, put in null the connection id.
     *
     * @return void
     */
    public function close(): void {
        if ($this->_connId) {
            $this->_close();
            $this->_connId = null;
        }
    }

    // --------------------------------------------------------------------

    /**
     * The following methods need to be overloaded by the identically named
     * methods in the platform-specific connection driver .
     */

    /**
     * Set client character set
     *
     * @param string $p_charset see database docs for accepted charsets.
     *
     * @return    bool false if cant set the character encoding
     */
    protected abstract function _set_charset(string $p_charset): bool;

    // --------------------------------------------------------------------

    /**
     * @return object|null basically a resource from the db
     */
    protected abstract function _open();

    // --------------------------------------------------------------------

    /**
     * Close the database connection.
     *
     * @return void
     */
    protected abstract function _close(): void;

}