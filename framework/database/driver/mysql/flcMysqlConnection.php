<?php

namespace framework\database\driver\mysql;


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

use framework\database\flcConnection;

/**
 * Database Connection Class specific for mysql database.
 *
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
class flcMysqlConnection extends flcConnection {

    // protected string $_host;
    // protected string $_port;
    // protected string $_database;
    // protected string $_user;
    protected ?string $_password = null;
    private string    $_socket;
    private int       $_client_flags;

    /**
     * MySQLi object
     *
     * Has to be preserved without being assigned to $conn_id.
     *
     * @var    \mysqli $_mysqli
     */
    protected ?\mysqli $_mysqli = null;

    /**
     * Compression flag
     *
     * @var    bool
     */
    public bool $compress = FALSE;


    /**
     * For mysql the dsn prototype will be : 'driver://username:password@hostname/database'
     *
     * @inheritDoc
     */
    public function initialize(?string $p_dsn, ?string $p_host, ?int $p_port, ?string $p_database, ?string $p_user, ?string $p_password, string $p_charset = 'utf8', string $p_collation = 'utf8_general_ci'): bool {
        // Extract dsn parts if well defined, if the values are on dsn they are taken otherwise extract
        // them from the parameters

        $query = '';
        if (($parsedDsn = @parse_url($p_dsn)) !== false) {
            $p_host = (isset($parsedDsn['host']) ? rawurldecode($parsedDsn['host']) : $p_host);
            $p_port = (isset($parsedDsn['port']) ? rawurldecode($parsedDsn['port']) : $p_port);
            $p_user = (isset($parsedDsn['user']) ? rawurldecode($parsedDsn['user']) : $p_user);
            $p_password = (isset($parsedDsn['pass']) ? rawurldecode($parsedDsn['pass']) : $p_password);
            $p_database = (isset($parsedDsn['database']) ? rawurldecode($parsedDsn['database']) : $p_database);
            $query = isset($parsedDsn['query']) ? rawurldecode($parsedDsn['query']) : "";

        }


        // Set default if values not defined , generate the full dsn for postgres
        $p_port = ($p_port ?? '3306');

        // Check values
        if (!isset($p_host) || !isset($p_user) || !isset($p_password) || !isset($p_database)) {
            return false;
        } else {
            //   $this->_host = $p_host;
            //  $this->_port = $p_port;
            //  $this->_user = $p_user;
            $this->_password = $p_password;
            //  $this->_database = $p_database;
            $this->_socket = '';


            // Do we have a socket path?
            if ($p_host[0] === '/') {
                $p_port = null;
                $this->_socket = $p_host;
                $p_host = null;
            }

            $this->_dsn = 'mysql://'.$p_user.':'.$p_password.'@'.$p_host.':'.$p_port.'/'.$p_database;
            if ($query && $query != "") {
                $this->_dsn .= '&'.$query;
            }
        }

        // preserve the collation
        if ($p_collation) {
            $this->_collation = $p_collation;
        }

        // preserve the charset
        if ($p_charset) {
            $this->_charset = $p_charset;
        }

        // Compress stuff ,
        $this->_client_flags = ($this->compress === TRUE) ? MYSQLI_CLIENT_COMPRESS : 0;
        $this->_mysqli = mysqli_init();

        $this->_mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 10);
        if (isset($this->stricton)) {
            if ($this->stricton) {
                $this->_mysqli->options(MYSQLI_INIT_COMMAND, 'SET SESSION sql_mode = CONCAT(@@sql_mode, ",", "STRICT_ALL_TABLES")');
            } else {
                $this->_mysqli->options(MYSQLI_INIT_COMMAND, 'SET SESSION sql_mode =
					REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
					@@sql_mode,
					"STRICT_ALL_TABLES,", ""),
					",STRICT_ALL_TABLES", ""),
					"STRICT_ALL_TABLES", ""),
					"STRICT_TRANS_TABLES,", ""),
					",STRICT_TRANS_TABLES", ""),
					"STRICT_TRANS_TABLES", "")');
            }
        }

        // ssl stuff
        // Important : to set encrypt after the class creation do :
        // $c = new FlcMysqlConnection();
        // $c->encrypt['ssl_key'=> key,..........];
        // $c->initialize(........params);
        //
        if (is_array($this->encrypt)) {
            $ssl = [];
            empty($this->encrypt['ssl_key']) or $ssl['key'] = $this->encrypt['ssl_key'];
            empty($this->encrypt['ssl_cert']) or $ssl['cert'] = $this->encrypt['ssl_cert'];
            empty($this->encrypt['ssl_ca']) or $ssl['ca'] = $this->encrypt['ssl_ca'];
            empty($this->encrypt['ssl_capath']) or $ssl['capath'] = $this->encrypt['ssl_capath'];
            empty($this->encrypt['ssl_cipher']) or $ssl['cipher'] = $this->encrypt['ssl_cipher'];

            if (!empty($ssl)) {
                if (isset($this->encrypt['ssl_verify'])) {
                    if ($this->encrypt['ssl_verify']) {
                        defined('MYSQLI_OPT_SSL_VERIFY_SERVER_CERT') && mysqli_options($this->_mysqli, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, TRUE);
                    }
                    // Apparently (when it exists), setting MYSQLI_OPT_SSL_VERIFY_SERVER_CERT
                    // to FALSE didn't do anything, so PHP 5.6.16 introduced yet another
                    // constant ...
                    //
                    // https://secure.php.net/ChangeLog-5.php#5.6.16
                    // https://bugs.php.net/bug.php?id=68344
                    elseif (defined('MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT')) {
                        $this->_client_flags |= MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT;
                    }
                }

                $this->_client_flags |= MYSQLI_CLIENT_SSL;
                $this->_mysqli->ssl_set($ssl['key'] ?? null, $ssl['cert'] ?? null, $ssl['ca'] ?? null, $ssl['capath'] ?? null, $ssl['cipher'] ?? null);
            }
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _set_charset(string $p_charset): bool {
        // Check if open is called before
        if ($this->_mysqli != null) {
            return $this->_mysqli->set_charset($p_charset);
        } else {
            return false;
        }
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _open() {
        if ($this->_mysqli->real_connect($this->get_host(), $this->get_user(), $this->_password, $this->get_database(), $this->get_port(), $this->_socket, $this->_client_flags)) {
            // Prior to version 5.7.3, MySQL silently downgrades to an unencrypted connection if SSL setup fails
            if (($this->_client_flags & MYSQLI_CLIENT_SSL) && version_compare($this->_mysqli->client_info, '5.7.3', '<=') && empty($this->_mysqli->query("SHOW STATUS LIKE 'ssl_cipher'")->fetch_object()->Value)) {
                $this->_mysqli->close();

                //log_message('error', $message);

                return false;
            }

            return $this->_mysqli;
        } else {
            return false;
        }


    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _close(): void {
        $this->_mysqli->close();
    }

}