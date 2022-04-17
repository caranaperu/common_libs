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
abstract class flcConnection {
    /**
     * Represents an connection id , this a resource that depends on each database.
     * @var resource|null
     */
    protected $connId;


    /**
     * Collation , basically for table creation.
     *
     * @var    string
     */
    protected string $collation = 'utf8_general_ci';

    /**
     * charset , if defined usaed on open connection.
     *
     * @var    string
     */
    protected string $charset = 'utf8';

    /**
     * @var string
     */
    protected string $dsn;


    /**
     *
     * initialize the data required for a connection using a dsn or separate parameters required for the connection.
     * IF dsn is incomplete the function will try to use the another parameters to complete
     * the required data.
     *
     * If no dsn is specified all others need to be.
     *
     * @param string | null $dsn Then dsn of the connection
     * @param string | null $host The hostname or ip address of the server.
     * @param int | null    $port The por number
     * @param string | null $database The database name to connect
     * @param string | null $user The user name
     * @param string | null $password The password
     * @param string        $charset The default charset to be used by the connection , default utf8
     * @param string        $collation The default collation for the connection , default utf8_general_ci
     *
     *
     * @return bool FALSE if not enough parameter data to create a correct dsn.
     */
    public abstract function initialize(?string $dsn, ?string $host, ?int $port, ?string $database, ?string $user, ?string $password, string $charset = 'utf8', string $collation = 'utf8_general_ci'): bool;

    /**
     * Extract the host from the dsn , if dsn is not initialized or host
     * is not defined return null.
     *
     * @return string|null The host value
     */
    public function getHost(): ?string {
        if ($this->dsn) {
            return parse_url($this->dsn, PHP_URL_HOST);
        }

        return null;
    }

    /**
     * Extract the port from the dsn , if dsn is not initialized or port
     * is not defined return null.
     *
     * @return string|null The port value
     */
    public function getPort(): ?string {
        if ($this->dsn) {
            $port = parse_url($this->dsn, PHP_URL_PORT);
            if ($port) {
                return strval($port);
            }
        }

        return null;
    }

    /**
     * Extract the user name  from the dsn , if dsn is not initialized or user
     * is not defined return null.
     *
     * @return string|null The user name  value
     */
    public function getUser(): ?string {
        if ($this->dsn) {
            return parse_url($this->dsn, PHP_URL_USER);
        }

        return null;
    }

    /**
     * Extract the database name from the dsn , if dsn is not initialized or database
     * is not defined return null.
     * The database for a db dsn is on the path of the url.
     *
     * @return string|null The database name value
     */
    public function getDatabase(): ?string {
        if ($this->dsn) {
            return parse_url($this->dsn, PHP_URL_PATH);
        }

        return null;
    }

    /**
     * @return resource|null
     */
    public function get_connection_id()  {
        return $this->connId;
    }

    /**
     * Set client character set
     *
     * @param string $charset see database docs for accepted charsets.
     *
     * @return    bool false if cant set the character encoding
     */
    protected abstract function _set_charset(string $charset): bool;

    /**
     * @return resource|null
     */
    protected abstract function _open();

    /**
     * Open the conecction used by this instance, if is already
     * a live conection the current one will be used, if this is not
     * the desired action call close methos first.
     *
     * @return bool true if can get connection or already one open , false otherwise
     */
    public function open(): bool {
        if (!isset($this->connId) || !$this->connId) {

            $conn = $this->_open();

            if (!$conn) {
                return false;
            }
            $this->connId = $conn;
        }


        $this->_set_charset($this->charset);
        return true;
    }

    protected abstract function _close(): void;

    /**
     * Close the connection, put in null the connection id.
     *
     * @return void
     */
    public function close(): void {
        if ($this->connId) {
            $this->_close();
            $this->connId = null;
        }
    }

}