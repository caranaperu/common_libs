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

use framework\database\driver\flcDriver;

/**
 * Mysql Driver Class
 *
 * This class extends the parent generic driver class: framework\database\driver\flcDriver
 * and is the specific one for mysql.
 *
 * Important : Part of this class is a modified one of driver class from codeigniter
 * all credits for his authors.
 *
 * Mysql doesnt support nested transactions, for use nested transaction apply
 * savepoints.
 *
 * @package        Database
 * @subpackage    Drivers
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
class flcMysqlDriver extends flcDriver {

    /**
     * DELETE hack flag
     *
     * Whether to use the MySQL "delete hack" which allows the number
     * of affected rows to be shown. Uses a preg_replace when enabled,
     * adding a bit more processing to all queries.
     *
     * @var    bool
     */
    public bool $delete_hack = TRUE;


    /**
     * @inheritdoc
     */
    public function get_db_driver(): string {
        return 'mysql';
    }

    // --------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function connect(): bool {
        return $this->conn->open();
    }

    // --------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function select_database(string $p_database): bool {
        if ($p_database === '') {
            $p_database = $this->get_connection()->get_database();
        }

        if ($this->get_connection()->get_connection_id()->select_db($p_database)) {
            $this->get_connection()->_set_database($p_database);

            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function error(): array {
        if (!empty($this->_mysqli->connect_errno)) {
            return [
                'code' => $this->_mysqli->connect_errno,
                'message' => $this->_mysqli->connect_error
            ];
        }

        return [
            'code' => $this->conn->get_connection_id()->errno,
            'message' => $this->conn->get_connection_id()->error
        ];
    }

    // --------------------------------------------------------------------

    /**
     * Prep the query
     *
     * If needed, each database adapter can prep the query string
     *
     * @param string $sql an SQL query
     *
     * @return    string
     */
    protected function _prep_query(string $p_sqlquery): string {
        // mysqli_affected_rows() returns 0 for "DELETE FROM TABLE" queries. This hack
        // modifies the query so that it a proper number of affected rows is returned.
        if ($this->delete_hack === TRUE && preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $p_sqlquery)) {
            return trim($p_sqlquery).' WHERE 1=1';
        }

        return $p_sqlquery;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _execute_qry(string $p_sqlquery) {
        return $this->get_connection()->get_connection_id()->query($this->_prep_query($p_sqlquery));
    }

    // --------------------------------------------------------------------

    /***************************************************************
     * Metadata
     */

    /**
     * @inheritdoc
     */
    protected function _list_columns_qry(string $p_table = ''): string {
        return 'SELECT COLUMN_NAME as column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '.$this->escape($p_table).' AND table_schema = '.$this->escape($this->conn->get_database());

    }

    /**
     * @inheritdoc
     */
    public function column_data(string $p_table, string $p_schema = ''): ?array {
        if ($p_schema == '') {
            $schema = $this->get_connection()->get_database();
        } else {
            $schema = $p_schema;
        }

        $sql = 'SELECT column_name, data_type, character_maximum_length,numeric_precision,column_default,column_key
			FROM INFORMATION_SCHEMA.COLUMNS
			WHERE table_schema = '.$this->escape($schema).' and table_name = '.$this->escape($p_table);

        if (($query = $this->execute_query($sql)) === null) {
            return null;
        }
        $query = $query->result_object();

        $retval = [];
        for ($i = 0, $c = count($query); $i < $c; $i++) {
            $retval[$i] = new \stdClass();
            $retval[$i]->name = $query[$i]->COLUMN_NAME;
            $retval[$i]->type = $query[$i]->DATA_TYPE;
            $retval[$i]->max_length = ($query[$i]->CHARACTER_MAXIMUM_LENGTH > 0) ? $query[$i]->CHARACTER_MAXIMUM_LENGTH : $query[$i]->NUMERIC_PRECISION;
            $retval[$i]->default = $query[$i]->COLUMN_DEFAULT;
            $retval[$i]->primary_key = (int)($query[$i]->COLUMN_KEY === 'PRI');
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function primary_key(string $p_table): ?string {
        // PGSQL way to obtain the primary key field name
        $qry = 'show columns from '.$p_table.' where `Key` = "PRI";';
        $RES = $this->execute_query($qry);

        if ($RES && $RES->num_rows() > 0) {
            return $RES->first_row()->Field;
        } else {
            return null;
        }

    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _column_data_qry(string $p_table): string {
        return 'SHOW COLUMNS FROM '.$p_table;
    }

    // --------------------------------------------------------------------

    protected function _list_tables_qry(string $p_schema, string $p_table_constraints = ''): string {
        $sql = 'select TABLE_NAME as table_name from information_schema.tables WHERE TABLE_SCHEMA=';


        // if the schema contains the databse name use it, else get from connection.
        if (trim($p_schema) == '') {
            $dbname = $this->get_connection()->get_database();

        } else {
            $dbname = $p_schema;

        }

        $sql .= $this->escape($dbname);

        if (isset($p_table_constraints) && $p_table_constraints !== '') {
            $sql .= ' AND table_name LIKE \'%'.$p_table_constraints.'%\'';
        }

        return $sql;
    }

    // --------------------------------------------------------------------

    /*************************************************************
     * Helpers
     */

    /**
     * @inheritdoc
     */
    protected function _escape_str(string $p_to_escape): string {
        return $this->conn->get_connection_id()->real_escape_string($p_to_escape);
    }


    // --------------------------------------------------------------------

    /***********************************************************************
     * Transaction managing
     */

    /**
     * @inheritdoc
     */
    protected function _trans_begin(): bool {
        $this->get_connection()->get_connection_id()->autocommit(FALSE);

        return version_compare(PHP_VERSION, '5.5', '>=') ? $this->get_connection()->get_connection_id()->begin_transaction() : $this->get_connection()->get_connection_id()->query('START TRANSACTION'); // can also be BEGIN or BEGIN WORK
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_commit(): bool {
        if ($this->get_connection()->get_connection_id()->commit()) {
            $this->get_connection()->get_connection_id()->autocommit(true);

            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_rollback(string $p_savepoint = ''): bool {
        if ($p_savepoint === '') {
            $ret = (bool)$this->conn->get_connection_id()->rollback();
        } else {
            $ret = (bool)$this->conn->get_connection_id()->query('ROLLBACK TO '.$p_savepoint);
        }

        /* if ($ret) {
            $this->conn->get_connection_id()->autocommit(true);
         }*/

        return $ret;
    }

    // --------------------------------------------------------------------

    /***
     * @inheritdoc
     */
    protected function _trans_savepoint(string $p_savepoint): bool {
        return (bool)$this->conn->get_connection_id()->savepoint($p_savepoint);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_remove_savepoint(string $p_savepoint): bool {
        return (bool)$this->conn->get_connection_id()->release_savepoint($p_savepoint);
    }

    // --------------------------------------------------------------------

}