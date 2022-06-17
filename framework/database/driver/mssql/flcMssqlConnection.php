<?php

namespace framework\database\driver\mssql;


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
 * Database Connection Class specific for ms sql database.
 *
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
class flcMssqlConnection extends flcConnection {

    protected ?string $_password = null;



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
    public function initialize(?string $p_dsn, ?string $p_host, ?int $p_port, ?string $p_database, ?string $p_user, ?string $p_password, string $p_charset = SQLSRV_ENC_CHAR, string $p_collation = 'SQL_Latin1_General_CP1_CI_AS'): bool {
        // Extract dsn parts if well defined, if the values are on dsn they are taken otherwise extract
        // them from the parameters

        // preserve the charset
        if ($p_charset) {
            $this->_charset = in_array(strtolower($p_charset), [
                'utf-8',
                'utf8'
            ], TRUE) ? 'UTF-8' : $p_charset;
        } else {
            $this->_charset = SQLSRV_ENC_CHAR;
        }

        /* if ($charset == 'UTF-8' && isset($p_collation)) {
             substr($p_collation, -strlen($needle))===$needle
         }*/

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
        $p_port = ($p_port ?? '1433');

        // if not user and password defined , try as a windows credentials login.
        if (!isset($p_user) && !isset($p_password)) {
            $p_user = '';
            $p_password = '';
        }

        // Check values
        if (!isset($p_host) || !isset($p_database)) {
            return false;
        } else {
            $this->_password = $p_password;

            $this->_dsn = 'mssql://'.$p_user.':'.$p_password.'@'.$p_host.':'.$p_port.'/'.$p_database;
            if ($query && $query != "") {
                $this->_dsn .= '&'.$query;
            }
        }

        // preserve the collation
        if ($p_collation) {
            $this->_collation = $p_collation;
        }


        return true;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _set_charset(string $p_charset): bool {
        // Not supported only can be set on the connection.
        return false;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _open($p_pooling = false) {
        $conn_info = [
            'UID' => $this->get_user(),
            'PWD' => $this->_password,
            'Database' => $this->get_database(),
            'ConnectionPooling' => ($p_pooling === TRUE) ? 1 : 0,
            'CharacterSet' => $this->_charset,
            'Encrypt' => ($this->encrypt === TRUE) ? 1 : 0,
            'ReturnDatesAsStrings' => 1
        ];

        // If the username and password are both empty, assume this is a
        // 'Windows Authentication Mode' connection.
        if (empty($conn_info['UID']) && empty($conn_info['PWD'])) {
            unset($conn_info['UID'], $conn_info['PWD']);
        }

        if (FALSE !== ($conn = sqlsrv_connect($this->get_host(), $conn_info))) {
            // Determine how identifiers are escaped
            $query = sqlsrv_query($conn,'SELECT CASE WHEN (@@OPTIONS | 256) = @@OPTIONS THEN 1 ELSE 0 END AS qi');
            $rows = sqlsrv_fetch_array($query,SQLSRV_FETCH_ASSOC);
            $quoted_identifier = empty($rows) ? FALSE : (bool)$rows['qi'];
            /*$this->_escape_char = ($quoted_identifier) ? '"' : [
                '[',
                ']'
            ];*/

            sqlsrv_free_stmt($query);
            unset($query);
        } else {
            print_r(sqlsrv_errors());
        }

        return $conn;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _close(): void {
        sqlsrv_close($this->_connId);
    }

}