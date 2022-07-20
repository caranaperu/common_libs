<?php

namespace framework\database\driver;

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
use framework\database\flcDbResult;
use framework\database\flcDbResultOutParams;
use framework\database\flcDbResults;
use framework\utils\flcStrUtils;

require_once dirname(__FILE__).'/../../utils/flcStrUtils.php';

/**
 * Generic Driver Class
 *
 * This is the base class for all specific database drivers.
 *
 * This driver try to manage nested transaction , but the behaviour depends on each
 * database,
 * For example:
 *          $con = new flc<dbid>>Connection();
 *          $con->initialize(....params);
 *          $ret = $driver->trans_start();
 *              $driver->execute_query("INSERT INTO a_table VALUES(value1_1, value1_2)");
 *              $ret = $driver->trans_start();
 *              $driver->execute_query("INSERT INTO a_table VALUES(value2_1, value2_")");
 *              $driver->rollback()
 *          $driver->trans_complete();
 *
 * In Mysql the result will be :
 *   a_table records :
 *          value_1_1,value1_2
 *
 * In Postgress the result will be
 *   a_table with empty records records
 *
 * The reason are :
 * 1) MySql do a commit when start a new transaction, from the documentation :
 *
 * "If autocommit mode is disabled within a session with SET autocommit = 0,
 *  the session always has a transaction open. A COMMIT or ROLLBACK statement ends
 *  the current transaction and a new one starts."
 *
 * 2) Postgres only add a counter each time a new transaction is created , that means
 * that all operation really is under the same transaction and only the outer rollback works
 * and over all the operations under transaction.
 * Other observation if that the firs rollback on all the "nested"  transactions , after that
 * after that all operations outside transaction is taken in autocommit mode.
 *
 * The manual says :
 * "Issuing BEGIN when already inside a transaction block will provoke a warning message.
 * The state of the transaction is not affected. To nest transactions within a transaction block,
 * use savepoints (see SAVEPOINT).
 *
 * Really its imposible to simulate autonomus transactions in databases that doesnt support ,
 * try to do that using savepoints its not a good answer , savepoints had other kind of problems
 * like excessive locks and much memory and time.
 *
 * 3) In ms sql server the docs say :
 * "If sqlsrv_begin_transaction is called after a transaction has already been initiated on the
 * connection but not completed by calling either sqlsrv_commit or sqlsrv_rollback, the call returns
 * false and an Already in Transaction error is added to the error collection."
 *
 * In other words rollback in any level of a transaction always rollback all the operations of all
 * levels at the point is called.
 * If you try to add records for example after that and try to commit , will fail because for
 * sql server no transaction is opened.
 *
 * -----------------------------------------------------------------------------------------------------
 * Nested transactions can introduce error conditions. For example, imagine you have a stored procedure
 * or trigger that contains a transaction that is rolled back. If the process calling the procedure also
 * uses a transaction, it's transaction could be unexpectedly rolled back by the stored procedure.
 * -----------------------------------------------------------------------------------------------------
 *
 *
 * The driver supports savepoints , the user can use that stuff.
 *
 *
 * Important : Part of this class is a modified one of driver class from codeigniter
 * all credits for his authors.
 *
 * @package        Database
 * @subpackage    Drivers
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
abstract class flcDriver {
    /**
     * @var string
     */
    private string $version;

    /**
     * Transaction enabled flag
     *
     * @var    bool
     */
    public bool $trans_enabled = true;

    /**
     * Strict transaction mode flag
     *
     * @var    bool
     */
    public bool $trans_strict = true;

    /**
     * Transaction depth level
     *
     * @var    int
     */
    protected int $_trans_depth = 0;

    /**
     * Transaction status flag
     *
     * Used with transactions to determine if a rollback should occur.
     *
     * @var    bool
     */
    protected bool $_trans_status = true;

    /**
     * Transaction failure flag
     *
     * Used with transactions to determine if a transaction has failed.
     *
     * @var    bool
     */
    protected bool $_trans_failure = false;

    /**
     * Transaction unique flag
     *
     * if its true begin transaction will be called one time
     * and all operations will be under a unique transaction,
     *
     * @var    bool
     */
    protected bool $_trans_unique = false;

    /**
     * Transaction unique begin flag
     *
     * if its true and _trans_unique is true indicates
     * that already teh unique transaction is already
     * open.
     *
     * @var    bool
     */
    private bool $_trans_unique_begin = false;

    /**
     * Transaction unique stop flag
     *
     * if its true is a signal to allow commit or rollback
     * in the unique transaction.
     *
     * @var    bool
     */
    private bool $_trans_unique_stop = false;


    /**
     * Transaction savepoints
     *
     * Used with transactions to manage save points list.
     *
     * @var    array for save the savepoints
     */
    protected array $_trans_savepoints = [];


    /**
     * ESCAPE character
     *
     * @var    string
     */
    protected string $_like_escape_chr = '!';

    /**
     * Identifier escape character
     *
     * @var    string
     */
    protected string $_escape_char = '"';

    /**
     * List of reserved identifiers
     *
     * Identifiers that must NOT be escaped.
     *
     * @var    string[]
     */
    protected array $_reserved_identifiers = ['*'];


    /**
     * connection class containing
     *                             connection data for the instance driver
     *
     * @var flcConnection
     */
    protected flcConnection $conn;

    public function __construct(flcConnection $p_conn) {
        $this->conn = $p_conn;
    }

    // --------------------------------------------------------------------

    /**
     * Return the version of the database
     *
     * @return string with the version
     */
    public function get_version(): ?string {
        if (!isset($this->version)) {
            $this->version = $this->_get_version();
        }

        return $this->version;
    }

    // --------------------------------------------------------------------

    /**
     * Return the database id, valid values are :
     * mysql,postgres,mssql
     *
     * @return string the database id, 'N/A' indicates not supported
     */
    public function get_db_driver(): string {
        return 'N/A';
    }

    // --------------------------------------------------------------------

    /**
     * Get the internal connection associated to the driver in constructor
     *
     * @return flcConnection
     */
    public function get_connection(): flcConnection {
        return $this->conn;
    }

    // --------------------------------------------------------------------

    /**
     * Get if the transaction is unique and not nested
     *
     * @return bool
     */
    public function is_trans_unique(): bool {
        return $this->_trans_unique;
    }

    // --------------------------------------------------------------------

    /**
     * Set if the transaction will be unique or not.
     * Call only one time before start a transaction or
     * after a trans_complete function.
     *
     * @param bool $is_trans_unique
     *
     * @return void
     */
    public function set_trans_unique(bool $is_trans_unique) {
        // Only can be changed if not transaction are active now.
        if ($this->_trans_depth == 0) {
            $this->_trans_unique = $is_trans_unique;
            $this->_trans_unique_stop = false;
            $this->_trans_unique_begin = false;
        }
    }


    // --------------------------------------------------------------------

    /**
     *
     * Connect to a database using the data in flcConnection.
     * Previously flcConnection $conn nee to be initialized
     *
     * @return bool true if is connected , false if not.
     */
    public abstract function connect(): bool;

    // --------------------------------------------------------------------

    /**
     *
     * Disconnect from a database using the data in flcConnection.
     * Previously flcConnection $conn need to be initialized
     *
     * @return void
     */
    public function disconnect(): void {
        // Probably redundant elimination.
        if ($this->trans_enabled && $this->_trans_depth > 0) {
            for ($i = $this->_trans_depth - 1; $i >= 0; $i--) {
                if (isset($this->_trans_savepoints[$i]) && count($this->_trans_savepoints[$i])) {
                    foreach ($this->_trans_savepoints[$i] as $trans_savepoint) {
                        unset($trans_savepoint);
                    }
                }
            }
        }
        $this->_trans_savepoints = [];
        $this->conn->close();
    }

    // --------------------------------------------------------------------

    /**
     * Some db servers require select an specific database before use in a query
     * those need to do a valid implementation of this method , others return true
     * is enough.
     *
     * @param string $p_database the database name
     *
     * @return bool true if selected
     */
    public abstract function select_database(string $p_database): bool;

    // --------------------------------------------------------------------

    /**
     * Execute a query , if obtain results load the result manager driver
     * for the specific database.
     *
     * @param string $p_sqlqry the query to execute
     *
     * @return flcDbResult|null
     */
    public function execute_query(string $p_sqlqry): ?flcDbResult {
        if (trim($p_sqlqry) === '') {
            return null;
        }

        if (false === ($result_id = $this->_execute_qry($p_sqlqry))) {
            // This will trigger a rollback if transactions are being used
            if ($this->_trans_depth !== 0) {
                $this->_trans_status = false;
            }

            return null;
        } else {
            // Load and instantiate the result driver
            $result_driver = $this->load_result_driver();

            return new $result_driver($this, $result_id);
        }

    }

    // --------------------------------------------------------------------

    /**
     * "Count All" query
     *
     * Generates a platform-specific query string that counts all records in
     * the specified database
     *
     * @param string $p_table the table name
     *
     * @return    int
     */
    public function count_all(string $p_table = ''): int {
        if ($p_table === '') {
            return 0;
        }

        $query = $this->execute_query($this->_get_count_all_sql().$this->escape_identifiers('numrows').' FROM '.$p_table);
        if (!$query or $query->num_rows() === 0) {
            return 0;
        }

        $query = $query->row();

        return (int)$query->numrows;
    }

    // --------------------------------------------------------------------


    /**
     * Load the result driver for the specific database.
     *
     * @return    string    the name of the result class
     */
    public function load_result_driver(): string {
        $driver = 'flc'.ucwords($this->get_db_driver()).'Result';
        $driver_class = 'framework\database\driver\\'.$this->get_db_driver().'\\'.$driver;

        if (!class_exists($driver_class, false)) {
            require_once('flcDbResult.php');
            require_once('./driver/'.$this->get_db_driver().'/'.$driver.'.php');
        }

        return $driver_class;
    }

    // --------------------------------------------------------------------

    /***********************************************************************
     * Metadata
     */

    /**
     * Determine if a particular field exists
     *
     * @param string $p_fieldname
     * @param string $p_tablename
     *
     * @return    bool
     */
    public function column_exists(string $p_fieldname, string $p_tablename): bool {
        return in_array($p_fieldname, $this->list_columns($p_tablename));
    }

    // --------------------------------------------------------------------

    /**
     * Returns an array of table names
     *
     * @param string $p_schema database schema to search in.
     * @param string $p_table_constraints for a "like" search
     *
     * @return    array
     */
    public function list_tables(string $p_schema, string $p_table_constraints = ''): array {
        $tables_list = [];
        $query = $this->execute_query($this->_list_tables_qry($p_schema, $p_table_constraints));

        if ($query) {
            foreach ($query->result_array() as $row) {
                $tables_list[] = $row['table_name'];
            }
        }

        return $tables_list;
    }

    // --------------------------------------------------------------------

    /**
     * Determine if a particular table exists
     *
     * @param string $p_schema database schema to search in.
     * @param string $p_table
     *
     * @return    bool
     */
    public function table_exists(string $p_schema, string $p_table): bool {
        return in_array($p_table, $this->list_tables($p_schema, $p_table));
    }

    // --------------------------------------------------------------------

    /**
     * Fetch Field Names.
     * The database dependant _list_columns_qry have o return each key of
     * the array as 'column_name'
     *
     * The answer have the output form of:
     *
     *     (
     *          [0] => sys_systemcode
     *          [1] => menu_id
     *          [2] => menu_codigo
     *          [3] => menu_descripcion
     *          [4] => menu_accesstype
     *      )
     *
     * @param string $p_table Table name
     *
     * @return    array
     */
    public function list_columns(string $p_table): ?array {

        // null if not supported in the database
        if (null === ($sql = $this->_list_columns_qry($p_table))) {
            return null;
        }
        if (null == ($query = $this->execute_query($sql))) {
            return null;
        }

        $columns = [];
        // For each key exist an entry as 'column_name'=>column name text
        foreach ($query->result_array() as $key => $value) {
            $columns[] = $value['column_name'];
        }

        return $columns;
    }

    // --------------------------------------------------------------------

    /**
     * primary_key
     *
     * Retrieves the primary key. It assumes that the row in the first
     * position is the primary key.
     * If the database support how to obtain the primary key need to override
     * this method.
     *
     * @param string $p_table Table name
     *
     * @return    string
     */
    public function primary_key(string $p_table): ?string {
        $fields = $this->list_columns($p_table);

        return is_array($fields) && count($fields) > 0 ? array_values(array_values($fields)[0])[0] : null;
    }

    // --------------------------------------------------------------------

    /**
     * Returns an object with column data , this is the default implementation
     * and need to be override for specific databases.
     * Each database use in different ways the schema , for
     * postgresql : The schemas are inside the databases , this is why for example the
     * information_schema is inside the database and exist one for each.
     * mysql : The schemas are the databases , this is why the information_schema
     * is global and act as a database.
     *
     * @param string $p_table the table name
     * @param string $p_schema the db schema , is not defined will use the default
     * schema for the db use in the connection.
     *
     * @return    array|null
     */
    public function column_data(string $p_table, string $p_schema = ''): ?array {
        $query = $this->execute_query($this->_column_data_qry($p_table));

        return ($query) ? $query->field_data() : null;
    }

    /**
     * Get Last error.
     * The specific driver implementation can override this method.
     *
     * @return    array
     */
    public function error(): array {
        return [
            'code' => null, 'message' => null
        ];
    }

    // --------------------------------------------------------------------

    /*******************************************************
     * Helpers
     */


    // --------------------------------------------------------------------

    /**
     * Determines if a query is a "write" type.
     *
     * @param string    An SQL query string
     *
     * @return    bool
     */
    public function is_write_type(string $sql): bool {
        return (bool)preg_match('/^\s*"?(SET|INSERT|UPDATE|DELETE|REPLACE|CREATE|DROP|TRUNCATE|LOAD|COPY|ALTER|RENAME|GRANT|REVOKE|LOCK|UNLOCK|REINDEX)\s/i', $sql);
    }

    /**
     * "Smart" Escape String
     *
     * Escapes data based on type, sets boolean and null types.
     * This is a default implementation an need to be override if its necessary.
     *
     * Example :
     *     print_r($driver->escape(100));
     *     print_r($driver->escape(100.00));
     *     print_r($driver->escape(true));
     *     print_r($driver->escape('test'));
     *     print_r($driver->escape(null));
     *
     * the results are 100,100.00,1,'test',NULL
     *
     * For arrays like this :
     *     print_r(['test',false,550]);
     *
     * the results are:
     *     Array
     *     (
     *      [0] => 'test'
     *      [1] => 0
     *      [2] => 550
     *     )
     *
     * @param mixed
     *
     * @return    mixed
     */
    public function escape($p_to_escape) {
        if (is_array($p_to_escape)) {
            // recursive
            return array_map([
                &$this, 'escape'
            ], $p_to_escape);
        } elseif (is_string($p_to_escape) or (is_object($p_to_escape) && method_exists($p_to_escape, '__toString'))) {
            return "'".$this->escape_str($p_to_escape)."'";
        } elseif (is_bool($p_to_escape)) {
            return ($p_to_escape === false) ? 0 : 1;
        } elseif ($p_to_escape === null) {
            return 'NULL';
        }

        return $p_to_escape;
    }

    // --------------------------------------------------------------------

    /**
     * Escape String.
     * The returned strings do NOT include quotes around them.
     * if $like is true prepend $_like_escape_chr value if the symbols
     * $_like_escape_chr,'_','$' exist on the string for use in a like
     * condition , for example :
     * SELECT * FROM emp WHERE ENAME LIKE 'J%$_%' ESCAPE '$';
     *
     * Usage example :
     *     print_r($driver->escape_str("Chicago O'Hare",true)).PHP_EOL;
     *
     * output :
     * Chicago O''Hare
     *
     * @param string|string[] $p_to_escape Input string
     * @param bool            $p_like Whether or not the string will be used in a LIKE condition
     *
     * @return    string|string[]
     */
    public function escape_str($p_to_escape, bool $p_like = false) {
        if (is_array($p_to_escape)) {
            foreach ($p_to_escape as $key => $val) {
                $p_to_escape[$key] = $this->escape_str($val, $p_like);
            }

            return $p_to_escape;
        }

        $p_to_escape = $this->_escape_str($p_to_escape);

        // escape LIKE condition wildcards
        if ($p_like === true) {
            return str_replace([
                $this->_like_escape_chr, '%', '_'
            ], [
                $this->_like_escape_chr.$this->_like_escape_chr, $this->_like_escape_chr.'%',
                $this->_like_escape_chr.'_'
            ], $p_to_escape);
        }

        return $p_to_escape;
    }

    // --------------------------------------------------------------------

    /**
     * Escape the SQL Identifiers
     *
     * This function escapes column and table names
     * This is generic , override if its necessary
     *
     * @param string|array $p_item
     *
     * @return    string|array
     */
    public function escape_identifiers($p_item) {

        if ($this->_escape_char === '' or empty($p_item) or in_array($p_item, $this->_reserved_identifiers)) {
            return $p_item;
        } elseif (is_array($p_item)) {
            foreach ($p_item as $key => $value) {
                $p_item[$key] = $this->escape_identifiers($value);
            }

            return $p_item;
        } // Avoid breaking functions and literal values inside queries
        elseif (ctype_digit($p_item) or strpos($p_item, '(') !== false or (is_array($p_item) && ($p_item[0] === "'" || ($this->_escape_char !== '"' && $p_item[0] === '"')))) {
            return $p_item;
        }

        static $preg_ec = [];

        if (empty($preg_ec)) {
            if (is_array($this->_escape_char)) {
                $preg_ec = [
                    preg_quote($this->_escape_char[0], '/'), preg_quote($this->_escape_char[1], '/'),
                    $this->_escape_char[0], $this->_escape_char[1]
                ];
            } else {
                $preg_ec[0] = $preg_ec[1] = preg_quote($this->_escape_char, '/');
                $preg_ec[2] = $preg_ec[3] = $this->_escape_char;
            }
        }

        foreach ($this->_reserved_identifiers as $id) {
            if (strpos($p_item, '.'.$id) !== false) {
                return preg_replace('/'.$preg_ec[0].'?([^'.$preg_ec[1].'\.]+)'.$preg_ec[1].'?\./i', $preg_ec[2].'$1'.$preg_ec[3].'.', $p_item);
            }
        }

        return preg_replace('/'.$preg_ec[0].'?([^'.$preg_ec[1].'\.]+)'.$preg_ec[1].'?(\.)?/i', $preg_ec[2].'$1'.$preg_ec[3].'$2', $p_item);
    }

    // --------------------------------------------------------------------


    /*******************************************************
     * Query creation
     */


    /**
     * Generate an insert string
     *
     * @param string $p_table the table upon which the query will be performed
     * @param array  $p_data an associative array data of key/values
     *
     * @return    string
     */
    public function insert_string(string $p_table, array $p_data): string {
        $fields = $values = [];

        foreach ($p_data as $key => $val) {
            $fields[] = $this->escape_identifiers($key);
            $values[] = $this->escape($val);
        }

        return $this->_insert($this->escape_identifiers($p_table), $fields, $values);
    }

    // --------------------------------------------------------------------

    /**
     * Generate an update string
     *
     * @param string $p_table the table upon which the query will be performed
     * @param array  $p_data an associative array data of key/values
     * @param mixed  $p_where the "where" statement
     * @param int    $p_limit
     *
     * @return    string
     */
    public function update_string(string $p_table, array $p_data, $p_where, int $p_limit = 0): string {
        if (empty($p_where)) {
            return false;
        }

        $fields = [];
        foreach ($p_data as $key => $val) {
            $fields[$this->escape_identifiers($key)] = $this->escape($val);
        }

        return $this->_update($this->escape_identifiers($p_table), $fields, $p_where, $p_limit);
    }

    // --------------------------------------------------------------------

    protected string       $_callable_procedure_call_string       = 'call';
    protected array        $_callable_procedure_sep_string        = ['(', ')'];
    protected string       $_callable_function_call_string_scalar = 'select';
    protected string       $_callable_function_call_string        = 'select * from';
    protected string       $_callable_function_sep_string         = '()';
    protected static array $_cast_conversion                      = [];


    public function cast_param(string $p_param, string $p_type, ?string $p_appendstr): string {
        $conv = '';
        $type = strtolower($p_type);
        if (array_key_exists($type, static::$_cast_conversion)) {
            if (static::$_cast_conversion[$type][0] !== '') {
                $conv = 'cast('.$p_param.' as '.static::$_cast_conversion[$type][0].$p_appendstr.')';
            }
        }

        if ($conv === '') {
            $conv = $p_param;
        }

        return $conv;
    }

    /**
     * @param string     $p_sp_name
     * @param string     $p_type
     * @param array|null $p_parameters
     *
     * @return string
     */
    public function callable_string(string $p_sp_name, string $p_type, string $p_type_return, ?array $p_parameters = null, ?array $p_use_casts = null): string {
        $sql = '';
        if ($p_type == 'procedure') {
            $sql = $this->_callable_procedure_call_string.' '.$p_sp_name.$this->_callable_procedure_sep_string[0];

        } else {
            if ($p_type_return == 'scalar') {
                $sql = $this->_callable_function_call_string_scalar.' '.$p_sp_name.$this->_callable_function_sep_string[0];
            } else {
                $sql = $this->_callable_function_call_string.' '.$p_sp_name.$this->_callable_function_sep_string[0];
            }
        }

        if ($p_parameters && count($p_parameters) > 0) {
            for ($i = 0; $i < count($p_parameters); $i++) {
                // determine if parameter is an array or simple and get the value.
                if (is_array($p_parameters[$i])) {
                    $value = $p_parameters[$i][0];
                } else {
                    $value = $p_parameters[$i];
                }
                if (isset($p_use_casts) && isset($p_use_casts[$i])) {
                    // If it is an array we expect [type,appendstr] in the array , otherwise only the type
                    if (is_array($p_use_casts[$i])) {
                        $type = $p_use_casts[$i][0];
                        $appendstr = $p_use_casts[$i][1];
                    } else {
                        $type = $p_use_casts[$i];
                        $appendstr = '';
                    }

                    if (array_key_exists($type, static::$_cast_conversion)) {
                        if (static::$_cast_conversion[$type][1] == 't') {
                            $sqlvalue = '\''.$value.'\'';
                        } elseif (static::$_cast_conversion[$type][1] == 'n') {
                            $sqlvalue = $value;
                        } elseif (static::$_cast_conversion[$type][1] == 'b') {
                            if ($value === 1 or $value === '1' or $value === 'true' or $value === 'TRUE' or $value === true or $value == 't') {
                                $sqlvalue = static::$_cast_conversion[$type][2] === 1 ? '1' : 'true';

                            } else {
                                $sqlvalue = static::$_cast_conversion[$type][2] === 1 ? '0' : 'false';
                            }
                        } else {
                            $sqlvalue = is_string($value) ? '\''.$value.'\'' : $value;

                        }

                        $sqlvalue = $this->cast_param($sqlvalue, $type, $appendstr);
                    } else {
                        $sqlvalue = $value;

                    }

                    $sql .= $sqlvalue.',';
                } else {
                    if ($value === true || $value === false) {
                        $sql .= ($value === true ? 'true' : 'false').',';
                    } else {
                        $sql .= $value.',';
                    }


                }
            }
            $sql = substr($sql, 0, strlen($sql) - 1);


        }
        if ($p_type == 'procedure') {
            $sql .= $this->_callable_procedure_sep_string[1].';';

        } else {
            $sql .= $this->_callable_function_sep_string[1].';';
        }


        return $sql;
    }

    // --------------------------------------------------------------------

    protected array $_parameter_types = ['IN_PARAM', 'OUT_PARAM', 'INOUT_PARAM'];

    public function execute_function(string $p_fn_name, ?array $p_parameters = null, ?array $p_use_casts = null): ?flcDbResult {

        $sqlinfo = /** @lang TSQL */
            'SELECT sm.object_id,
                           OBJECT_NAME(sm.object_id) AS object_name,
                           o.type,
                           o.type_desc,
                           sm.definition,
                           sm.uses_ansi_nulls,
                           sm.uses_quoted_identifier,
                           sm.is_schema_bound,
                           sm.execute_as_principal_id
                        FROM sys.sql_modules AS sm
                        JOIN sys.objects AS o ON sm.object_id = o.object_id
                        WHERE sm.object_id = OBJECT_ID(\''.$p_fn_name.'\')
                        ORDER BY o.type;';

        $type = null;
        if ($res = $this->_execute_qry($sqlinfo)) {
            $type = sqlsrv_fetch_array($res)['type_desc'];
            sqlsrv_free_stmt($res);
        } else {
            return null;
        }

        if ($type && $type == 'SQL_SCALAR_FUNCTION') {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', 'scalar', $p_parameters, $p_use_casts);
        } else {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', 'records', $p_parameters, $p_use_casts);

        }
        echo $sqlfunc.PHP_EOL;

        return $this->execute_query($sqlfunc);

    }

    public const FLCDRIVER_PROCTYPE_RESULTSET      = 1;
    public const FLCDRIVER_PROCTYPE_MULTIRESULTSET = 2;
    public const FLCDRIVER_PROCTYPE_VALUE          = 4;
    public const FLCDRIVER_PROCTYPE_OUTP           = 8;

    public const FLCDRIVER_PARAMTYPE_IN    = 'IN';
    public const FLCDRIVER_PARAMTYPE_INOUT = 'INOUT';
    public const FLCDRIVER_PARAMTYPE_OUT   = 'OUT';

    public function execute_stored_procedure(string $p_fn_name, string $p_type, ?array $p_parameters = null, ?array $p_use_casts = null): ?flcDbResults {

        $params = [];
        $oparams = [];
        $outparams_count = 0;
        $sqlpre = '';
        $sqlpost = '';

        if ($p_parameters && count($p_parameters) > 0) {
            for ($i = 0; $i < count($p_parameters); $i++) {
                // determine if parameter is an array or simple and get the value.
                if (is_array($p_parameters[$i])) {
                    $sqlpre = '';
                    $sqlpost = '';

                    // take the parameters descriptors.
                    $value = $p_parameters[$i][0];
                    $paramtype = $p_parameters[$i][1] ?? '';
                    $sqltype = $p_parameters[$i][2] ?? '';

                    if ($p_type == self::FLCDRIVER_PROCTYPE_VALUE) {
                        $sqlpre .= 'DECLARE @p'.$i.' '.$sqltype.';';
                        $sqlpost .= 'SELECT @p'.$i.';';
                        $params[] = $value;
                    } elseif (($p_type & self::FLCDRIVER_PROCTYPE_OUTP) == self::FLCDRIVER_PROCTYPE_OUTP) {
                        if ($paramtype == self::FLCDRIVER_PARAMTYPE_OUT || $paramtype == self::FLCDRIVER_PARAMTYPE_INOUT) {
                            ${'p'.$outparams_count} = (is_string($value) ? '\''.$value.'\'' : $value);
                            $oparams[] = [&${'p'.$outparams_count}, SQLSRV_PARAM_OUT];
                            $params[] = '?';
                            $outparams_count++;
                        } else {
                            $params[] = $value;
                        }
                    }

                } else {
                    $params[] = (is_string($p_parameters[$i]) ? '\''.$p_parameters[$i].'\'' : $p_parameters[$i]);
                }
            }
        }

        // create sp store results
        require_once('flcDbResults.php');
        $results = new flcDbResults();


        // Get the callable string
        // No cast allowed in mssql server on stored procedures calls, ignore the parameter
        $sqlfunc = $this->callable_string($p_fn_name, 'procedure', 'records', $params);


        // Process the stored procedurre
        //

        $is_resultset = ($p_type & self::FLCDRIVER_PROCTYPE_RESULTSET) == self::FLCDRIVER_PROCTYPE_RESULTSET;
        $is_multiresultset = ($p_type & self::FLCDRIVER_PROCTYPE_MULTIRESULTSET) == self::FLCDRIVER_PROCTYPE_MULTIRESULTSET;
        $is_outparams = ($p_type & self::FLCDRIVER_PROCTYPE_OUTP) == self::FLCDRIVER_PROCTYPE_OUTP;

        // If only a sp with a single return value?
        // generate the sql code and execute
        if (($p_type & self::FLCDRIVER_PROCTYPE_VALUE) == self::FLCDRIVER_PROCTYPE_VALUE) {
            $sqlfunc = $sqlpre.$sqlfunc.$sqlpost;
            $sqlfunc = str_replace($this->_callable_procedure_call_string.' ', $this->_callable_procedure_call_string.' @p0 = ', $sqlfunc);
            echo $sqlfunc.PHP_EOL;

            $res = $this->execute_query($sqlfunc);
            $results->add_resultset_result($res);

        } // For stores procedures with output parameters o resultset or both
        elseif ($is_outparams || $is_resultset || $is_multiresultset) {
            echo $sqlfunc.PHP_EOL;


            // execute
            $res = sqlsrv_query($this->get_connection()->get_connection_id(), $sqlfunc, $oparams);
            if ($res) {
                // If a resulset type sp , get the results.
                if ($is_resultset || $is_multiresultset) {

                    // In single resultsets , we don prefetch the answers , is not required and also is more
                    // efficient.
                    if (!$is_outparams && $is_resultset) {
                        $result_driver = $this->load_result_driver();
                        $result = new $result_driver($this, $res);

                        $results->add_resultset_result($result);

                    } else {
                        // Get the resulset or resultsets
                        do {
                            $result_driver = $this->load_result_driver();
                            $result = new $result_driver($this, $res);

                            // Yes , we need to pre-fetch al records , inefficient yes , but required in case we have to fetch the output parametes.
                            // Info on docs : "make sure all result sets are stepped through,  since the output params may not be set until this happens"
                            // Also we can never get the next resulset results without prefetching the previous ones.
                            // Bad , bad , but no other way.
                            $result->result_array();

                            $results->add_resultset_result($result);
                        } while (sqlsrv_next_result($res));

                    }
                }

                // If we ned to process output params?
                if ($is_outparams) {
                    require_once('flcDbResultOutParams.php');

                    $outparams = new flcDbResultOutParams();

                    if (isset($oparams) && count($oparams) > 0) {
                        for ($i = 0; $i < count($oparams); $i++) {
                            $outparams->add_out_param('p'.$i, ${'p'.$i});
                        }

                    }

                    $results->add_outparams_result($outparams);
                }


            }

        }

        return $results;

    }

    public function execute_stored_procedure_mysql(string $p_fn_name, string $p_type, ?array $p_parameters = null, ?array $p_use_casts = null): ?flcDbResults {

        $params = [];
        $outparams_count = 0;
        $sqlpre = '';
        $sqlpost = '';

        if ($p_parameters && count($p_parameters) > 0) {

            for ($i = 0; $i < count($p_parameters); $i++) {
                // determine if parameter is an array or simple and get the value.
                if (is_array($p_parameters[$i])) {


                    // take the parameters descriptors.
                    $value = $p_parameters[$i][0];
                    $paramtype = $p_parameters[$i][1] ?? '';
                    $sqltype = $p_parameters[$i][2] ?? '';


                    if (($p_type & self::FLCDRIVER_PROCTYPE_OUTP) == self::FLCDRIVER_PROCTYPE_OUTP) {
                        if ($paramtype == self::FLCDRIVER_PARAMTYPE_OUT || $paramtype == self::FLCDRIVER_PARAMTYPE_INOUT) {

                            $outparams_count++;

                            $sqlpre .= (strlen($sqlpre) > 0 ? ';' : '');
                            $sqlpre .= 'set @p'.$i.'='.(is_string($value) ? '\''.$value.'\'' : $value);
                            $sqlpost .= (strlen($sqlpost) > 0 ? ',@p'.$i : 'select @p'.$i);

                            $params[] = '@p'.$i;
                        } else {
                            $params[] = (is_string($value) ? '\''.$value.'\'' : $value);
                        }
                    } else {
                        $params[] = (is_string($p_parameters[$i]) ? '\''.$p_parameters[$i].'\'' : $p_parameters[$i]);

                    }

                } else {
                    $params[] = (is_string($p_parameters[$i]) ? '\''.$p_parameters[$i].'\'' : $p_parameters[$i]);
                }
            }
        }

        // create sp store results
        require_once('flcDbResults.php');
        $results = new flcDbResults();


        // Get the callable string
        $sqlfunc = $this->callable_string($p_fn_name, 'procedure', 'records', $params, $p_use_casts);
        if ($sqlpre) {
            $sqlfunc = $sqlpre.';'.$sqlfunc;

        }
        if ($sqlpost) {
            $sqlfunc = $sqlfunc.$sqlpost.';';

        }
        echo $sqlfunc;

        $mysqli = $this->get_connection()->get_connection_id();
        $res = $mysqli->multi_query($sqlfunc);

        if ($res) {
            $results_count = 0;
            do {
                if ($result = $mysqli->store_result()) {
                    $result_driver = $this->load_result_driver();
                    $result = new $result_driver($this, $result);

                    $results->add_resultset_result($result);

                    $results_count++;
                }
            } while ($mysqli->more_results() && $mysqli->next_result());

            if ($outparams_count > 0) {
                $result = $results->get_resultset_result($results_count - 1);
                if ($result) {
                    require_once('flcDbResultOutParams.php');

                    $outparams = new flcDbResultOutParams();


                    if ($result->num_rows() > 0) {
                        foreach ($result->result_array() as $row) {
                            foreach ($row as $key => $value) {
                                $outparams->add_out_param($key, $value);

                            }
                        }

                        $results->add_outparams_result($outparams);
                    }
                    $results->resultset_free_result($results_count - 1);

                }


            }
            return $results;

        } else {
            return null;
        }


    }

    /*******************************************************
     * Transaction support
     */

    // --------------------------------------------------------------------

    /**
     * Disable Transactions
     * This permits transactions to be disabled at run-time.
     *
     * @return    void
     */
    public function trans_off() {
        $this->trans_enabled = false;
    }

    // --------------------------------------------------------------------

    /**
     * Enable/disable Transaction Strict Mode
     *
     * When strict mode is enabled, if you are running multiple groups of
     * transactions, if one group fails all subsequent groups will be
     * rolled back.
     *
     * If strict mode is disabled, each group is treated autonomously,
     * meaning a failure of one group will not affect any others
     *
     * @param bool $p_mode = true
     *
     * @return    void
     */
    public function trans_strict(bool $p_mode = true): void {
        $this->trans_strict = is_bool($p_mode) ? $p_mode : true;
    }

    // --------------------------------------------------------------------

    /**
     * Start Transaction
     *
     * @param bool $p_test_mode = false
     *
     * @return    bool
     */
    public function trans_start(bool $p_test_mode = false): bool {
        if (!$this->trans_enabled) {
            return false;
        }

        return $this->trans_begin($p_test_mode);
    }

    // --------------------------------------------------------------------

    /**
     * Complete Transaction
     *
     * @return    bool
     */
    public function trans_complete(): bool {
        if (!$this->trans_enabled) {
            return false;
        }

        $ret = false;

        // Flag to allow commit or rollback execution under unique transaction
        if ($this->_trans_unique) {
            $this->_trans_unique_stop = true;
        }

        // The query() function will set this flag to false in the event that a query failed
        if ($this->_trans_status === false or $this->_trans_failure === true) {
            $this->trans_rollback();

            // If we are NOT running in strict mode, we will reset
            // the _trans_status flag so that subsequent groups of
            // transactions will be permitted.
            if ($this->trans_strict === false) {
                $this->_trans_status = true;
            }

            // log_message('debug', 'DB Transaction Failure');

        } else {
            $ret = $this->trans_commit();
        }

        // Because after the rollback/commit all open transactions are finished , we remove all savepoints
        // on the control array if exists and put the trans_count on 0.
        if (isset($this->_trans_savepoints) && count($this->_trans_savepoints) > 0) {
            $count = count($this->_trans_savepoints);
            for ($i = $count - 1; $i >= 0; $i--) {
                array_splice($this->_trans_savepoints[$i], 0);
                unset($this->_trans_savepoints[$i]);

            }
        }
        $this->_trans_depth = 0;
        if ($this->_trans_unique) {
            $this->_trans_unique_begin = false;
            $this->_trans_unique_stop = false;
        }

        return $ret;
    }

    // --------------------------------------------------------------------

    /**
     * Lets you retrieve the transaction flag to determine if it has failed
     *
     * @return    bool
     */
    public function trans_status(): bool {
        return $this->_trans_status;
    }

    // --------------------------------------------------------------------

    /**
     * Begin Transaction
     *
     * @param bool $test_mode
     *
     * @return    bool
     */
    public function trans_begin(bool $test_mode = false): bool {
        if ($this->trans_enabled) {

            // Reset the transaction failure flag.
            // If the $test_mode flag is set to true transactions will be rolled back
            // even if the queries produce a successful result.
            $this->_trans_failure = ($test_mode === true);


            // If not under only one transaction or no current open transaction
            // begin a new transaction.
            $ret = true;
            if (!$this->_trans_unique or !$this->_trans_unique_begin) {
                if ($ret = $this->_trans_begin()) {
                    if ($this->_trans_unique) {
                        $this->_trans_unique_begin = true;
                    }
                }

            }

            if ($ret) {
                if ($this->_trans_unique) {
                    if (!isset($this->_trans_savepoints[0])) {
                        $this->_trans_savepoints[0] = [];
                    }
                } else {
                    $this->_trans_savepoints[$this->_trans_depth] = [];
                    $this->_trans_depth++;
                }

            }


            return $ret;

        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Commit Transaction
     *
     * @return    bool
     */
    public function trans_commit(): bool {
        if (!$this->trans_enabled || ($this->_trans_depth == 0 && !$this->_trans_unique_stop)) {
            return false;
        } // When transactions are unique commit and remove the savepoints
        elseif ($this->_trans_unique) {
            $ret = true;
            // Only commit one time under unique transaction
            if ($this->_trans_unique_stop) {
                if ($ret = $this->_trans_commit()) {
                    if (isset($this->_trans_savepoints[0])) {
                        unset($this->_trans_savepoints[0]);
                        $this->_trans_depth = 0;
                    }
                }
            }

            return $ret;

        } elseif (/*$this->_trans_depth > 1 or*/ $this->_trans_commit()) {
            // Remove savepoints array involved in the current transaction.
            // Some databases allow one commit after another without send an error
            // we need to take account the trans depth value here.
            if ($this->_trans_depth > 0) {
                $this->_trans_depth--;
                unset($this->_trans_savepoints[$this->_trans_depth]);
            }

            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * set a save savepoint Transaction.
     * If already exist do nothing otherwise associate to the current depth.
     *
     * @param string $savepoint
     *
     * @return    bool true if not already exist and savepoint executed correctly.
     */
    public function trans_savepoint(string $savepoint): bool {
        if (!$this->trans_enabled) {
            return false;
        } // When transactions are nested associate to the current one
        elseif ($this->_trans_depth > 0 or $this->_trans_unique) {
            $pos = $this->_trans_unique ? 0 : $this->_trans_depth - 1;

            if (!isset($this->_trans_savepoints[$pos]) || !in_array($savepoint, $this->_trans_savepoints[$pos])) {
                if ($this->_trans_savepoint($savepoint)) {
                    $this->_trans_savepoints[$pos][] = $savepoint;

                    return true;
                }
            }
        }

        return false;
    }

    /**
     * set a save savepoint Transaction.
     * If already exist do nothing otherwise associate to the current depth.
     *
     * @param string $p_savepoint
     *
     * @return    bool true if exist and execute correctly
     */
    public function trans_remove_savepoint(string $p_savepoint): bool {
        if (!$this->trans_enabled) {
            return false;
        } // When transactions are nested associate to the current one
        elseif ($this->_trans_depth > 0 or $this->_trans_unique) {
            $pos = $this->_trans_unique ? 0 : $this->_trans_depth - 1;

            // if save point exist in a group of transaction?
            if (in_array($p_savepoint, $this->_trans_savepoints[$pos])) {
                // Get the index
                if (($index = array_search($p_savepoint, $this->_trans_savepoints[$pos])) !== false) {
                    // execute
                    if ($this->_trans_remove_savepoint($p_savepoint)) {
                        // remove the savepoint from array
                        unset($this->_trans_savepoints[$pos][$index]);

                        return true;
                    }
                }
            }
        }

        return false;
    }


    // --------------------------------------------------------------------

    /**
     * Rollback Transaction
     *
     * @param string $p_savepoint savepoint name
     *
     * @return    bool
     */
    public function trans_rollback(string $p_savepoint = ''): bool {
        if (!$this->trans_enabled or $this->_trans_depth === 0) {
            return false;
        } // When transactions are nested we only begin/commit/rollback the outermost ones
        else {
            // Its a rollback to savepoint?
            if ($p_savepoint != '') {
                $pos = $this->_trans_unique ? 0 : $this->_trans_depth - 1;

                // If exist the savepoint?
                if (in_array($p_savepoint, $this->_trans_savepoints[$pos])) {
                    if ($this->_trans_rollback($p_savepoint)) {

                        $index = array_search($p_savepoint, $this->_trans_savepoints[$pos]);
                        unset($this->_trans_savepoints[$pos][$index]);


                        return true;
                    }

                }
            } else {
                $ret = true;

                if ($this->_trans_unique) {
                    if ($this->_trans_unique_stop) {
                        $ret = $this->_trans_rollback();
                    }
                } elseif ($ret = $this->_trans_rollback()) { // Standard rollback
                    if ($this->_trans_depth > 0) {
                        $pos = $this->_trans_unique ? 0 : $this->_trans_depth - 1;

                        // rollback removes all savepoints defined in the middle automatically , we need to remove elements in the array
                        if (isset($this->_trans_savepoints[$pos]) /*&& count($this->_trans_savepoints[$pos])*/) {
                            array_splice($this->_trans_savepoints[$pos], 0);
                            unset($this->_trans_savepoints[$pos]);
                        }
                    }
                    $this->_trans_depth--;
                }


                return $ret;
            }
        }

        return false;
    }


    /*******************************************************
     * DB dependant methods
     */

    /**
     * Execute the query in low level , the return is a resource that depends of each
     * database that contain the answer.
     *
     * @param string $p_sqlquery an SQL query
     *
     * @return object|bool the object is basically a resource db dependant
     */
    protected

    abstract function _execute_qry(string $p_sqlquery);


    // --------------------------------------------------------------------

    /**
     * Version number query string
     *
     * @return    string
     */
    protected function _version_qry(): string {
        return 'SELECT VERSION() AS ver';
    }

    // --------------------------------------------------------------------

    /**
     * Database version number
     *
     * Returns a string containing the version of the database being used.
     * Most drivers will override this method.
     *
     * @return    string
     */
    protected function _get_version(): string {
        $res = $this->execute_query($this->_version_qry())->row();
        if (!$res->ver || $res->ver == '') {
            return 'N/A';
        }

        return $res->ver;
    }

    // --------------------------------------------------------------------

    /**
     * Execute the query in low level , the return is a resource that depends of each
     * database that contain the answer.
     *
     * @return    string
     */
    protected function _get_count_all_sql(): string {
        return 'SELECT COUNT(*) AS ';
    }

    // --------------------------------------------------------------------

    /**
     * List column query
     *
     * Generates a platform-specific query string so that the column names can be fetched
     * The column name field has to be "column_name
     *
     * @param string $p_table the table name
     *
     * @return    string
     */
    abstract protected function _list_columns_qry(string $p_table = ''): string;

    // --------------------------------------------------------------------

    /**
     *
     * Generates a platform-specific query string so that the table names can be fetched.
     * The table name field has to be "table_name
     *
     * @param string $p_schema database schema to search in.
     * @param string $p_table_constraints with constraints for like search
     *
     * @return    string
     */
    abstract protected function _list_tables_qry(string $p_schema, string $p_table_constraints = ''): string;

    // --------------------------------------------------------------------

    /**
     * Field data query
     *
     * Generates a platform-specific query so that the column data can be retrieved
     *
     * @param string $p_table
     *
     * @return    string
     */
    abstract protected function _column_data_qry(string $p_table): string;

    // --------------------------------------------------------------------

    /**
     * Rollback Transaction
     * Execute the beginning of the transaction that depends of
     * each database.
     *
     * @return    bool
     */
    abstract protected function _trans_begin(): bool;

    // --------------------------------------------------------------------

    /**
     * Rollback Transaction
     * Execute the rollback of the transaction that depends of
     * each database.
     *
     * @param string $p_savepoint if defined the roolback will ber to the savepoint
     *
     * @return    bool
     */
    abstract protected function _trans_rollback(string $p_savepoint = ''): bool;

    // --------------------------------------------------------------------

    /**
     * Rollback Commit
     * Execute the commit of the transaction that depends of
     * each database.
     *
     * @return    bool
     */
    abstract protected function _trans_commit(): bool;

    // --------------------------------------------------------------------

    /**
     * savepoint transaction
     * Execute the savepoint of the transaction that depends of
     * each database.
     *
     * @param string $p_savepoint the name
     *
     * @return    bool
     */
    abstract protected function _trans_savepoint(string $p_savepoint): bool;

    // --------------------------------------------------------------------

    /**
     * remove savepoint transaction
     * Execute the remove  savepoint of the transaction that depends of
     * each database.
     * Not all databases implement this method in low level for example
     * ms sql server and oracle doesnt support this stuff, use with caution
     * if portability is your goal.
     *
     * @param string $p_savepoint the name
     *
     * @return    bool false fail or not supported
     */
    abstract protected function _trans_remove_savepoint(string $p_savepoint): bool;

    // --------------------------------------------------------------------

    /**
     * Platform-dependant string escape
     *
     * @param string $p_to_escape
     *
     * @return    string
     */
    protected function _escape_str(string $p_to_escape): string {
        return str_replace("'", "''", flcStrUtils::remove_invisible_characters($p_to_escape));
    }

    // --------------------------------------------------------------------

    /**
     * Insert statement
     *
     * Generates a platform-specific insert string from the supplied data
     *
     * @param string $p_table the table name
     * @param array  $p_keys the insert keys
     * @param array  $p_values the insert values
     *
     * @return    string
     */
    protected function _insert(string $p_table, array $p_keys, array $p_values): string {
        return 'INSERT INTO '.$p_table.' ('.implode(', ', $p_keys).') VALUES ('.implode(', ', $p_values).')';
    }

    // --------------------------------------------------------------------

    /**
     * Update statement
     *
     * Generates a platform-specific update string from the supplied data
     *
     * @param string $p_table the table name
     * @param array  $p_values the update data
     * @param string $p_where the where clause
     * @param int    $p_limit the maximun number of records to update
     *
     * @return    string
     */
    protected function _update(string $p_table, array $p_values, string $p_where, int $p_limit = 0): string {
        $valstr = [];
        foreach ($p_values as $key => $val) {
            $valstr[] = $key.' = '.$val;
        }

        return 'UPDATE '.$p_table.' SET '.implode(', ', $valstr).' WHERE '.$p_where.' '.($p_limit > 0 ? ' LIMIT '.$p_limit : '');
    }

}


