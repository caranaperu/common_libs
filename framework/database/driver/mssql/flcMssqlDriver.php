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

use framework\database\driver\flcDriver;
use framework\database\flcConnection;

/**
 * Microsoft sql  Driver Class (based on sqlsrv)
 *
 * This class extends the parent generic driver class: framework\database\driver\flcDriver
 * and is the specific one for ms sql.
 *
 * Important : Part of this class is a modified one of driver class from codeigniter
 * all credits for his authors.
 *
 *
 * @package        Database
 * @subpackage    Drivers
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
class flcMssqlDriver extends flcDriver {

    /**
     * Scrollable flag
     *
     * Determines what cursor type to use when executing queries.
     *
     * FALSE or SQLSRV_CURSOR_FORWARD would increase performance,
     * but would disable num_rows() (and possibly insert_id())
     *
     * @var	mixed
     */
    public $scrollable= null;

    /**
     * Class constructor
     *
     * @param array $params
     *
     * @return    void
     */
    public function __construct(flcConnection $p_conn) {
        parent::__construct($p_conn);

        // This is only supported as of SQLSRV 3.0
        // Recommendation left as null , otherwise some functions like get the numrows or isert_id will
        // fail as microsoft document.
        // @link https://docs.microsoft.com/en-us/sql/connect/php/cursor-types-sqlsrv-driver?view=sql-server-ver16
        //
        if ($this->scrollable === NULL) {
            $this->scrollable = defined('SQLSRV_CURSOR_CLIENT_BUFFERED') ? SQLSRV_CURSOR_CLIENT_BUFFERED : FALSE;
        }
    }


    /**
     * @inheritdoc
     */
    public function get_db_driver(): string {
        return 'mssql';
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

        if (($res = $this->_execute_qry('USE '.$this->escape_identifiers($p_database)))) {
            sqlsrv_free_stmt($res);
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
        $error = [
            'code' => '00000',
            'message' => ''
        ];
        $sqlsrv_errors = sqlsrv_errors(SQLSRV_ERR_ERRORS);

        if (!is_array($sqlsrv_errors)) {
            return $error;
        }

        $sqlsrv_error = array_shift($sqlsrv_errors);
        if (isset($sqlsrv_error['SQLSTATE'])) {
            $error['code'] = isset($sqlsrv_error['code']) ? $sqlsrv_error['SQLSTATE'].'/'.$sqlsrv_error['code'] : $sqlsrv_error['SQLSTATE'];
        } elseif (isset($sqlsrv_error['code'])) {
            $error['code'] = $sqlsrv_error['code'];
        }

        if (isset($sqlsrv_error['message'])) {
            $error['message'] = $sqlsrv_error['message'];
        }

        return $error;
    }


    // --------------------------------------------------------------------

    /**
     * IMPORTANT : If you are calling an stored procedure and need to get the error
     * generated by a THROW or RAISERROR and return false , always begin the sp
     * with SET NOCOUNT ON  !!!!!
     *
     * @inheritdoc
     */
    protected function _execute_qry(string $p_sqlquery) {
        return ($this->scrollable === FALSE OR $this->is_write_type($p_sqlquery))
            ? sqlsrv_query($this->conn->get_connection_id(), $p_sqlquery)
            : sqlsrv_query($this->conn->get_connection_id(), $p_sqlquery, NULL, array('Scrollable' => $this->scrollable));
    }

    // --------------------------------------------------------------------

    /***************************************************************
     * Metadata
     */

    /**
     * @inheritdoc
     */
    protected function _list_columns_qry(string $p_table = ''): string {
        return 'SELECT COLUMN_NAME as column_name
			FROM information_schema.Columns
			WHERE UPPER(TABLE_NAME) = '.$this->escape(strtoupper($p_table));
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function primary_key(string $p_table): ?string {
        // MSSQL way to obtain the primary key field name
        $qry = "select C.COLUMN_NAME FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS T
                    JOIN INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE C ON C.CONSTRAINT_NAME=T.CONSTRAINT_NAME
                WHERE
                C.TABLE_NAME=".$this->escape($p_table)." and T.CONSTRAINT_TYPE='PRIMARY KEY';";

        $RES = $this->execute_query($qry);

        if ($RES && $RES->num_rows() > 0) {
            $colname = $RES->first_row()->COLUMN_NAME;
            $RES->free_result();

            return $colname;
        } else {
            return null;
        }

    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _column_data_qry(string $p_table): string {
        return 'SELECT  * FROM '.$p_table.' LIMIT 1';
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function column_data(string $p_table, string $p_schema = ''): ?array {
        if ($p_schema == '') {
            $schema = 'dbo';
        } else {
            $schema = $p_schema;
        }

        $sql = 'SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH, NUMERIC_PRECISION, COLUMN_DEFAULT
			FROM INFORMATION_SCHEMA.Columns
			WHERE TABLE_SCHEMA = '.$this->escape($schema).' and UPPER(TABLE_NAME) = '.$this->escape(strtoupper($p_table));

        if (($query = $this->execute_query($sql)) === null) {
            return null;
        }
        $ans = $query->result_object();

        $retval = [];
        for ($i = 0, $c = count($ans); $i < $c; $i++) {
            $retval[$i] = new \stdClass();
            $retval[$i]->name = $ans[$i]->COLUMN_NAME;
            $retval[$i]->type = $ans[$i]->DATA_TYPE;
            $retval[$i]->max_length = ($ans[$i]->CHARACTER_MAXIMUM_LENGTH > 0) ? $ans[$i]->CHARACTER_MAXIMUM_LENGTH : $ans[$i]->NUMERIC_PRECISION;
            $retval[$i]->default = $ans[$i]->COLUMN_DEFAULT;
        }

        $query->free_result();

        return $retval;
    }

    /**
     * @inheritdoc
     */
    protected function _list_tables_qry(string $p_schema, string $p_table_constraints = ''): string {
        // if not set default to dbo.
        if (!isset($p_schema) || trim($p_schema) == '') {
            $p_schema = 'dbo';
        }

        $sql = "SELECT  TABLE_NAME as table_name FROM  INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=".$this->escape($p_schema);

        if (isset($p_table_constraints) && $p_table_constraints !== '') {
            $sql .= ' AND TABLE_NAME LIKE \'%'.$p_table_constraints.'%\'';
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
    protected function _version_qry(): string {
        return 'SELECT @@VERSION AS ver';
    }

    // --------------------------------------------------------------------

    /***********************************************************************
     * Transaction managing
     */

    /**
     * @inheritdoc
     */
    protected function _trans_begin(): bool {
        return sqlsrv_begin_transaction($this->conn->get_connection_id());
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_commit(): bool {
        return sqlsrv_commit($this->conn->get_connection_id());
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_rollback(string $p_savepoint = ''): bool {
        if ($p_savepoint === '') {
            return sqlsrv_rollback($this->conn->get_connection_id());
        } else {
            return (bool)sqlsrv_query($this->conn->get_connection_id(), 'ROLLBACK TRANSACTION '.$p_savepoint.';');
        }
    }

    // --------------------------------------------------------------------

    /***
     * @inheritdoc
     */
    protected function _trans_savepoint(string $p_savepoint): bool {
        $query = 'SAVE TRANSACTION '.$p_savepoint.';';
        echo $query.PHP_EOL;
        return (bool)sqlsrv_query( $this->conn->get_connection_id(),$query);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_remove_savepoint(string $p_savepoint): bool {
        // NOT SUPPORTED IN T_SQL
        return FALSE;
    }

    // --------------------------------------------------------------------

}