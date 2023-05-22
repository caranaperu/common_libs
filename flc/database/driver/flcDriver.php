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

namespace flc\database\driver;


use Exception;
use flc\database\flcDbResult;
use flc\database\flcDbResults;
use flc\flcCommon;
use RuntimeException;


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
 */
abstract class flcDriver {
    /**
     * If the driver will run outside the framework set as true otherwise
     * wil use the standard framework logger that include other clasees.
     *
     * @var bool
     */
    static bool $dblog_console = false;


    /**************************************************************
     * Connection variables
     */
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
    public $encrypt = false;

    /**
     * Compression flag
     *
     * @var    bool
     */
    public bool $compress = false;

    /**
     * database table/functions/procedures prefix
     * Can be used for compatibility purposes , some databases
     * like ms sql server use dbo as prefix for example , always
     * prepend your tables , etc with this prefix.
     *
     * @var    string
     */
    public string $dbprefix = '';

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
     * If false try to allow autonomous transactios
     * otherwise al operations are under one transaction.
     *
     * @var    bool
     */
    public bool $trans_strict = false;

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
    protected bool $_trans_unique = true;

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
     * For enable o disable debug
     *
     * @var bool true for enable debug
     */
    public bool $debug = true;


    /**************************************************************************
     * For stored procedure and  function support (callables)
     */
    protected string $_callable_procedure_call_string = 'call';

    protected string $_callable_function_call_string_scalar = 'select';
    protected string $_callable_function_call_string        = 'select * from';
    protected array  $_callable_function_sep_string         = ['(', ')'];
    protected array  $_callable_procedure_sep_string        = ['(', ')'];

    /**
     * Flag to indicate is casts on sp or function are supported
     * Some db like mssql doesnt support casts in callables
     * @var bool
     */
    protected bool $_cast_on_callables = true;

    /**
     * This array of casts need to overloaded en each database driver.
     * The form is :
     * 'type' => ['mapped_type','conversion_type']
     *
     * for example :
     *         'boolean' => ['BIT', 'b',1],
     *
     * If we cast to boolean , will be mappep to BIT and the conversion will be to
     * a number 0 or 1 , if the definition doesnt have the last value will be
     * converted to tru or false.
     *
     * another cases :
     *          'string' => ['VARCHAR', 't'],
     *
     * if we cast to string will be mapped to varchar and the value to cast will be
     * treated as text.
     *
     * @var array
     */
    protected static array $_cast_conversion = [];


    public const FLCDRIVER_PROCTYPE_RESULTSET      = 1;
    public const FLCDRIVER_PROCTYPE_MULTIRESULTSET = 2;
    public const FLCDRIVER_PROCTYPE_SCALAR         = 4;

    public const FLCDRIVER_PARAMTYPE_IN    = 'IN';
    public const FLCDRIVER_PARAMTYPE_INOUT = 'INOUT';
    public const FLCDRIVER_PARAMTYPE_OUT   = 'OUT';

    // --------------------------------------------------------------------

    /**
     * Constructor , the constructor allow to send the initial setup
     * for options , this array can contain one or more keys of :
     *   - db_debug for enable the debug stuff of the driver
     *   - stricton for strict mode on transactions
     *   - encrypt to allow encrypted connection if the database support it.
     *   - compress to allow compression on the connection if the database support it.
     *   - dbprefix prefix that can be used for prefix the tables/functions/sp
     *
     * @param array|null $p_options if null take defaults
     */
    public function __construct(?array $p_options = null) {
        // if the options are sended we take their values if exists on the
        // array , otherwise set defaults.
        if (isset($p_options)) {
            $this->debug = $p_options['db_debug'] ?? false;
            $this->trans_strict = $p_options['stricton'] ?? false;
            $this->encrypt = $p_options['encrypt'] ?? false;
            $this->compress = $p_options['compress'] ?? false;
            $this->dbprefix = $p_options['dbprefix'] ?? '';

        }
    }

    // --------------------------------------------------------------------

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
     * Open the connection used by this instance, if is already
     * a live connection the current one will be used, if this is not
     * the desired action call close method first.
     *
     * @return bool true if can get connection or already one open , false otherwise
     */
    public function open(): bool {
        if (!$this->is_open()) {

            $conn = $this->_open();

            if ($conn === false) {
                return false;
            }

            // from here $_mysqli = $_conn_id
            $this->_connId = $conn;
            pg_set_error_verbosity($this->_connId, PGSQL_ERRORS_TERSE);

        }


        $this->set_charset($this->_charset);

        return true;
    }

    /**
     * Verify if the connection is already open
     *
     * @return bool true the connection is open
     */
    public function is_open(): bool {
        if (!isset($this->_connId) || !$this->_connId) {
            return false;
        }

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
     * @return resource with the connection id
     */
    public function get_connection() {
        return $this->_connId;
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
     * Connect to a database alias for open
     *
     * @return bool true if is connected , false if not.
     */
    public abstract function connect(): bool;

    // --------------------------------------------------------------------

    /**
     *
     * Disconnect from a database
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
        $this->close();
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
            if ((!$this->_trans_unique && $this->_trans_depth !== 0) || ($this->_trans_unique && $this->_trans_unique_begin)) {
                $this->_trans_status = false;
            }

            $this->log_error('Cant execute query '.$p_sqlqry, 'E');

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
        if (trim($p_table) === '') {

            $this->log_error("Count all required a table name");

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
        $driver_class = 'flc\database\driver\\'.$this->get_db_driver().'\\'.$driver;

        if (!class_exists($driver_class, false)) {
            require_once(dirname(__FILE__).'/../'.'flcDbResult.php');
            require_once(dirname(__FILE__).'/'.$this->get_db_driver().'/'.$driver.'.php');
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
     * Retrieves the list of fields part of the primary key. It assumes that the row in the first
     * position is the primary key.
     * If the database support how to obtain the primary key need to override
     * this method.
     *
     * @param string $p_table Table name
     *
     * @return array with the list of primary keys or an empty array
     */
    public function primary_key(string $p_table): array {
        $fields = $this->list_columns($p_table);

        return is_array($fields) && count($fields) > 0 ? array_values(array_values($fields)[0])[0] : [];
    }

    // --------------------------------------------------------------------

    /**
     * Returns an array of column information for a given table and schema,
     * this is the default implementation and need to be override for specific databases.
     *
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

        if (($query = $this->execute_query($this->_column_data_qry($p_table, $p_schema))) === null) {
            return null;
        }

        $ans = $query->result_object();
        $query->free_result();

        return $ans;

    }

    /**
     * Get Last error.
     * The specific driver implementation can override this method.
     *
     * @return    array
     */
    public function error(): array {
        return [
            'code' => 0,
            'message' => ''
        ];
    }

    // --------------------------------------------------------------------

    /*******************************************************
     * Helpers
     */


    // --------------------------------------------------------------------


    /**
     * Get an input value and convert to an specific database rowversion.
     *
     * @param mixed $p_value
     *
     * @return mixed
     */
    abstract public function cast_to_rowversion($p_value);

    // --------------------------------------------------------------------

    /**
     * The driver support ilke operator?
     *
     * @return boolean false
     */
    public function is_ilike_supported(): bool {
        return false;
    }

    /**
     * For pagination purposes this method return the offset-limit clause
     * for each database , the limit will be calculate using the difference between
     * p_end_row-p_start_row.
     *
     * @param int $p_start_row start row need to be less than $p_end_row
     * @param int $p_end_row
     *
     * @return string with the clause or '' if p_start_row is >= p_end_row
     */
    public function get_limit_offset_str(int $p_start_row, int $p_end_row): string {
        if ($p_end_row - $p_start_row <= 0) {
            return '';
        }

        return 'limit '.($p_end_row - $p_start_row).' offset '.$p_start_row;
    }

    // --------------------------------------------------------------------

    /**
     * Determines if a query is a "write" type.
     *
     * @param string $sql An SQL query string
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
     * @param mixed $p_to_escape
     *
     * @return    mixed
     */
    public function escape($p_to_escape) {
        if (is_array($p_to_escape)) {
            // recursive
            return array_map([
                &$this,
                'escape'
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
                $this->_like_escape_chr,
                '%',
                '_'
            ], [
                $this->_like_escape_chr.$this->_like_escape_chr,
                $this->_like_escape_chr.'%',
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
                    preg_quote($this->_escape_char[0], '/'),
                    preg_quote($this->_escape_char[1], '/'),
                    $this->_escape_char[0],
                    $this->_escape_char[1]
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
     * @param string      $p_table the table upon which the query will be performed
     * @param array       $p_data an associative array data of key/values
     * @param string      $p_where the "where" statement
     * @param int         $p_limit the number of records to update
     * @param string|null $p_orderby the 'order by" clause
     *
     * @return    string
     */
    public function update_string(string $p_table, array $p_data, string $p_where, int $p_limit = 0, string $p_orderby = null): string {
        if (empty($p_where)) {
            return false;
        }

        $fields = [];
        foreach ($p_data as $key => $val) {
            $fields[$this->escape_identifiers($key)] = $this->escape($val);
        }

        return $this->_update($this->escape_identifiers($p_table), $fields, $p_where, $p_limit, $p_orderby);
    }

    // --------------------------------------------------------------------


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

    /**
     * Return if a transaction is already in progress
     * If we are orking under unique transaction the variable
     * _trans_unique_begin will be true , if under nested transaction
     * _trans_depth will be 0.
     *
     * @return bool true if yes.
     */
    public function is_trans_open(): bool {
        if ($this->_trans_unique) {
            return $this->_trans_unique_begin;
        } else {
            return $this->_trans_depth > 0;
        }
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
     * For artificial generate a transaction error , sometimes in our logic
     * we have bussiness errors on our logic but no database error, this method
     * allow to advice trans_complete that we need to rollback.
     *
     * @return    void
     */
    public function trans_mark_dirty() {
        if ($this->trans_enabled) {
            if ($this->_trans_unique_begin || $this->_trans_depth > 0) {
                $this->_trans_status = false;
            }
        }
    }

    // --------------------------------------------------------------------

    /**
     * Revert the transaction status to ok.
     *
     * @return    void
     */
    public function trans_mark_clean() {
        $this->_trans_status = true;
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
     * Set client character set
     *
     * @param string $p_charset see database docs for accepted charsets.
     *
     * @return    bool false if cant set the character encoding
     */
    public abstract function set_charset(string $p_charset): bool;

    // --------------------------------------------------------------------

    /**
     * @return resource|false basically a resource from the db
     */
    protected abstract function _open();

    // --------------------------------------------------------------------

    /**
     * Close the database connection.
     *
     * @return void
     */
    protected abstract function _close(): void;

    /**
     * Execute the query in low level , the return is a resource that depends of each
     * database that contain the answer.
     *
     * @param string $p_sqlquery an SQL query
     *
     * @return object|bool the object is basically a resource db dependant
     */
    protected abstract function _execute_qry(string $p_sqlquery);


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
        $res = $this->execute_query($this->_version_qry());
        $row = $res->first_row();
        if (!$row->ver || $row->ver == '') {
            return 'N/A';
        }

        $version = $row->ver;
        $res->free_result();

        return $version;
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
     * @param string      $p_table
     * @param string|null $p_schema database schema to search in.
     *
     *
     * @return    string
     */
    abstract protected function _column_data_qry(string $p_table, ?string $p_schema = null): string;

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
        return str_replace("'", "''", flcCommon::remove_invisible_characters($p_to_escape));
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
     * @param string      $p_table the table name
     * @param array       $p_values the update data
     * @param string      $p_where the where clause
     * @param int         $p_limit the maximun number of records to update
     * @param string|null $p_orderby order by clause
     *
     * @return    string
     */
    protected function _update(string $p_table, array $p_values, string $p_where, int $p_limit = 0, ?string $p_orderby = null): string {
        $valstr = [];
        foreach ($p_values as $key => $val) {
            $valstr[] = $key.' = '.$val;
        }

        if ($p_orderby === null) {
            $p_orderby = '';
        }

        return "UPDATE $p_table SET ".implode(', ', $valstr)." WHERE $p_where $p_orderby".($p_limit > 0 ? ' LIMIT '.$p_limit : '');
    }

    /***********************************************************************
     * Stored procedures and function support
     */

    /**
     *
     * Portable execution of a db functions.
     * Because the differences between implementations on diffrent databases , the
     * most portable way to use this method is only input parameters and a scalar
     * value return , this is the common factor between the 3 supported.
     * In other words in all other cases its better use an stored procedure or function
     * in postgres that act as a normal sp.
     *
     * This method also support 2 modes :
     * - Return a resultset (only mssql and postgress support this option)
     * - Return an scalar value
     *
     * For other cases use execute_procedure , and this is  important for postgres because support
     * all kind of functions or the new supported stored procedures.
     *
     * Also you can cast a parameter if its required.
     *
     * Important :
     *
     * Microsoft sql server :
     * Doesnt accept inout or out parameters.
     * Returns a value or a resultset.
     *
     * Mysql :
     * Doesnt accept inout or out parameters.
     * Doesnt not return resultset only scalar.
     *
     * Postgres :
     * Accept in , out and inout parameters
     * Returns a value or a resultset
     * Act as a procedure in other databases but no transactions.
     *
     * Real implementation on each specific db driver.
     *
     * @param string     $p_fn_name the function name
     * @param array|null $p_parameters array  of parameters in the form [value1,value2,...]
     *
     * For example:
     *      [1,2018,[''=>flcDriver::FLCDRIVER_PARAMTYPE_OUT],[3=>flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
     * @param array|null $p_casts array of casts, if null means no cast to use,.
     *
     * in the form [[$pos=>['type',toappend_str],[],.......] or [[$pos=>'type'],[$pos2=>'type'],.......]
     * or a mixed of each one. The pos is the position of the parameter in the parameters array.
     *
     * For example :
     *
     *      [0=> 'string',2=>'int',4=>['string','(20)']]
     * That means the declared parameter in position 0 cast to string , the second in position to integer and the fourth
     * to string , also append (20) to the cast.
     *
     * The results can be : cast(param0 as VARCHAR), cast(param2 as INTEGER) , cast(param4 as VARCHAR(20) of course
     * the final syntax depends on the specific database.
     *
     * @return flcDbResult|null with the answers , null on error.
     *
     * @see flcDbResult
     *
     *
     */
    public function execute_function(string $p_fn_name, ?array $p_parameters = null, ?array $p_casts = null): ?flcDbResult {
        return null;
    }

    /**
     * Executes an stored procedure based on the sp name (or function name in postgress) and the parameters to be used
     * and also casts if required.
     *
     * Ms SQL Server
     * Doesnt allow casts in stored procedures calls.
     * Allow all types of input paramenters (in,inout,out)
     * Can return none,single or multiple resultsets.
     * Can return single values. (scalar)
     * Can return scalar, single or multiple resultsets with output parameters also.
     *
     * MySQL
     * Allow all types of input paramenters (in,inout,out)
     * Can return none,single or multiple resultsets.
     * Can return single values. (scalar)
     * Can return scalar, single or multiple resultsets with output parameters also.
     *
     * Postgres.
     * If using functions:
     * Allow all types of input paramenters (in,inout,out)
     * Can return none,single or multiple resultsets (sort of).
     * Can return single values. (scalar)
     * Can return scalar, single or multiple resultsets with output parameters also.
     *
     * If using Stored procedures (Postgres 13+)
     * Allow all types of input paramenters (in,inout,out)
     * Can only return out parameters.
     *
     * In postgres either on functions or stored procedures the only way to obtain multiple resulsets
     * are via out parameters , specific refcursors
     *
     * i.e : CREATE OR REPLACE PROCEDURE p_getMultipleResultset(inout ioparam int,ref1 refcursor,ref2 refcursor)
     *
     * each refcursor can contain a resulset.
     * This driver support refcursors and return in flcDbResults each one as a normal resultset used in other
     * databases.
     *
     * The call need to be something like this:
     *          $query = $driver->execute_stored_procedure('p_getMultipleResultset',
     * flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
     *                      3,
     *                      ['ref1',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor'],
     *                      ['ref2',flcDriver::FLCDRIVER_PARAMTYPE_OUT,'refcursor']
     *                  ]);
     *
     * Of course if you need to create support for multiples db with the same code , you dont need to do nothing ,
     * because for other databases refcursor will be skipped, ie for the same call the generate code in
     * sql server will be :
     *
     * EXEC p_getMultipleResultset 3
     *
     * and for mysql :
     *
     * CALL p_getMultipleResultset( 3 )
     *
     * ignoring the refcursors because other dbs supported can return multiple resultsets in the expected way.
     *
     * But its recommended that refcursors in the pgsql code are the last parameters.
     *
     * @param string     $p_fn_name the sp or function name (postgres)
     * @param string     $p_type : FLCDRIVER_PROCTYPE_RESULTSET,FLCDRIVER_PROCTYPE_MULTIRESULTSET or
     *     FLCDRIVER_PROCTYPE_SCALAR
     * @param array|null $p_parameters array  of parameters in the form [value1,value2,...]
     *
     * For example:
     *
     *      [1,2018,[''=>flcDriver::FLCDRIVER_PARAMTYPE_OUT],[3=>flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
     * @param array|null $p_casts array of casts, if null means no casts to use.
     *
     * in the form [[$pos=>['type',toappend_str],[],.......] or [[$pos=>'type'],[$pos2=>'type'],.......]
     * or a mixed of each one. The pos is the position of the parameter in the parameters array.
     *
     * For example :
     *
     *      [0=> 'string',2=>'int',4=>['string','(20)']]
     * That means the declared parameter in position 0 cast to string , the second in position to integer and the fourth
     * to string , also append (20) to the cast.
     * The results can be : cast(param0 as VARCHAR), cast(param2 as INTEGER) , cast(param4 as VARCHAR(20) of course
     * the final syntax depends on the specific database.
     *
     * @return flcDbResults|null the results , can contain multiple resulsets and also output parameters. Null on error
     *
     * @see flcDbResults
     */
    public function execute_stored_procedure(string $p_fn_name, string $p_type, ?array $p_parameters = null, ?array $p_casts = null): ?flcDbResults {

        // pgsql functions no soporta retornar valores si hay  out parameters . A menos que el tipo de retorno coincida con los  (in)out parameters
        // stored proedures soporta el retorno de refcursors y (in)out parameters a la vez , por el mismo motivo que los stored procedures en postgress no tienen
        // clausula return.
        return null;
    }


    /**
     * Executes an stored procedure/function based on the sp or function name and also casts if required.
     * The advantage f this method over execute_stored_procedure / execute_function if this method resolve
     * both cases , also we dont need to know the direction of the parameters only we need to
     * pass the name=>value pair for each input or inout parameters.
     *
     *
     * Ms SQL Server
     * Doesnt allow casts in stored procedures calls.
     * Allow all types of input paramenters (in,inout,out)
     * Can return none,single or multiple resultsets.
     * Can return single values. (scalar)
     * Can return scalar, single or multiple resultsets with output parameters also.
     *
     * MySQL
     * Allow all types of input paramenters (in,inout,out)
     * Can return none,single or multiple resultsets.
     * Can return single values. (scalar)
     * Can return scalar, single or multiple resultsets with output parameters also.
     *
     * Postgres.
     * If using functions:
     * Allow all types of input paramenters (in,inout,out)
     * Can return none,single or multiple resultsets (sort of).
     * Can return single values. (scalar)
     * Can return scalar, single or multiple resultsets with output parameters also.
     *
     * If using Stored procedures (Postgres 13+)
     * Allow all types of input paramenters (in,inout,out)
     * Can only return out parameters.
     *
     * In postgres either on functions or stored procedures the only way to obtain multiple resulsets
     * are via out parameters , specific refcursors
     *
     * i.e : CREATE OR REPLACE PROCEDURE p_getMultipleResultset(inout ioparam int,ref1 refcursor,ref2 refcursor)
     *
     * each refcursor can contain a resulset.
     * This driver support refcursors and return in flcDbResults each one as a normal resultset used in other
     * databases.
     *
     * The call need to be something like this:
     *          $query = $driver->execute_stored_procedure('p_getMultipleResultset',
     * flcDriver::FLCDRIVER_PROCTYPE_MULTIRESULTSET, [
     *                      3,
     *                      'ref1' => 'ref1',
     *                      'ref2' => 'ref2'
     *                  ]);
     *
     * Of course if you need to create support for multiples db with the same code , you dont need to do nothing ,
     * because for other databases refcursor will be skipped, ie for the same call the generate code in
     * sql server will be :
     *
     * EXEC p_getMultipleResultset 3
     *
     * and for mysql :
     *
     * CALL p_getMultipleResultset( 3 )
     *
     * ignoring the refcursors because other dbs supported can return multiple resultsets in the expected way.
     *
     * But its recommended that refcursors in the pgsql code are the last parameters.
     *
     * @param string     $p_callable_name the sp or function name (postgres)
     * @param string     $p_type : FLCDRIVER_PROCTYPE_RESULTSET,FLCDRIVER_PROCTYPE_MULTIRESULTSET or
     *     FLCDRIVER_PROCTYPE_SCALAR
     * @param array|null $p_params_values array  of parameters in the form [param1=>value1,param2=>value2,...]
     *
     * For example:
     *
     *      [1,2018,[''=>flcDriver::FLCDRIVER_PARAMTYPE_OUT],[3=>flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
     * @param array|null $p_exclude_casts array of exclude to cast parameters, if null means nothing to exclude.*
     * in the form [$param_name,$param_name,.......]
     *
     * @return flcDbResults|null the results , can contain multiple resulsets and also output parameters. Null on error
     *
     * @see flcDbResults
     */
    public function execute_callable(string $p_callable_name, string $p_type, ?array $p_params_values = null, ?array $p_exclude_casts = null): ?flcDbResults {

        // pgsql functions no soporta retornar valores si hay  out parameters . A menos que el tipo de retorno coincida con los  (in)out parameters
        // stored proedures soporta el retorno de refcursors y (in)out parameters a la vez , por el mismo motivo que los stored procedures en postgress no tienen
        // clausula return.
        return null;
    }


    /**
     * Get the Insert ID
     * The table name and field name are required both in some databases like
     * postgress.
     *
     * @param string|null $p_table_name
     * @param string|null $p_column_name
     *
     * @return int with the id
     */
    abstract public function insert_id(?string $p_table_name = null, ?string $p_column_name = null): int;

    /**
     * @param string      $p_param the value to cast
     * @param string      $p_type string,float,char,etc
     * @param string|null $p_appendstr to append at the end of cast . ie: (20)
     * @param bool        $p_is_mapped_cast is true use mapped cast otherwise use the type directly.
     *
     * @return string  ie: cast(200 as VARCHAR(20)) (db dependant)
     */
    public function cast_param(string $p_param, string $p_type, ?string $p_appendstr = null, bool $p_is_mapped_cast = true): string {
        $conv = '';
        // if not is a mapped cast append type directly
        if (!$p_is_mapped_cast) {
            $conv = 'cast('.$p_param.' as '.$p_type.$p_appendstr.')';
        } else {
            $type = strtolower($p_type);
            if (array_key_exists($type, static::$_cast_conversion)) {
                if (static::$_cast_conversion[$type][0] !== '') {
                    $conv = 'cast('.$p_param.' as '.static::$_cast_conversion[$type][0].$p_appendstr.')';
                }
            }
        }

        if ($conv === '') {
            $conv = $p_param;
        }

        return $conv;
    }

    /**
     * Generate the callable string , function or procedure
     *
     * @param string     $p_sp_name the name of the function or stored procedure
     * @param string     $p_type 'procedure' or 'function'
     * @param string     $p_type_return 'scalar' or 'records'
     * @param array|null $p_parameters array  of parameters in the form [value1,value2,...]
     *
     * Fore example:
     *
     *      [1,2018,[3=>flcDriver::FLCDRIVER_PARAMTYPE_INOUT]
     *
     * for the third element only the number 3 will be taken the param type will be ignored
     * and is supported to do more easy the job when is called from other methods.
     *
     * @param array|null $p_casts array of casts, if null means no casts to use
     *
     * in the form [[$pos=>['type',toappend_str],[],.......] or [[$pos=>'type'],[$pos2=>'type'],.......]
     * or a mixed of each one. The pos is the position of the parameter in the parameters array.
     *
     * For example :
     *
     *      [0=> 'string',2=>'int',4=>['string','(20)']]
     *
     * That means the declared parameter in position 0 cast to string , the second in position to integer and the fourth
     * to string , also append (20) to the cast.
     *
     * The results can be : cast(param0 as VARCHAR), cast(param2 as INTEGER) , cast(param4 as VARCHAR(20) of course
     * the final syntax depends on the specific database.
     *
     * @return string with the callable string.
     */
    public function callable_string(string $p_sp_name, string $p_type, string $p_type_return, ?array $p_parameters = null, ?array $p_casts = null): string {
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


                // casts to process?
                if ($p_casts !== null && isset($p_casts[$i])) {
                    // If it is an array we expect [type,appendstr] in the array , otherwise only the type
                    if (is_array($p_casts[$i])) {
                        $type = $p_casts[$i][0];
                        $appendstr = $p_casts[$i][1];
                    } else {
                        $type = $p_casts[$i];
                        $appendstr = '';
                    }

                    // normalize the value
                    if (array_key_exists($type, static::$_cast_conversion)) {
                        if (static::$_cast_conversion[$type][1] == 't') {
                            $sqlvalue = '\''.$value.'\'';
                        } elseif (static::$_cast_conversion[$type][1] == 'n') {
                            $sqlvalue = $value;
                        } elseif (static::$_cast_conversion[$type][1] == 'b') {
                            if ($value === 1 or $value === '1' or strtoupper($value) === 'TRUE' or $value === true or strtoupper($value) == 'T' or strtoupper($value) == 'YES') {
                                $sqlvalue = static::$_cast_conversion[$type][2] === 1 ? '1' : 'true';

                            } else {
                                $sqlvalue = static::$_cast_conversion[$type][2] === 1 ? '0' : 'false';
                            }
                        } else {
                            // special hack for output parameters that need to be represented as ?
                            if ($value === '<outparam=?>') {
                                $sqlvalue = '?';

                            } else {
                                $sqlvalue = is_string($value) ? '\''.$value.'\'' : $value;
                            }

                        }

                        // cast the value
                        $sqlvalue = $this->cast_param($sqlvalue, $type, $appendstr);
                    } else {
                        $sqlvalue = $value;

                    }

                    $sql .= $sqlvalue.',';
                } else {
                    if ($value === true || $value === false) {
                        $sql .= ($value === true ? 'true' : 'false').',';
                    } else {
                        if ($value === null) {
                            $sqlvalue = 'NULL';
                        } else {
                            // special hack for output parameters that need to be represented as ?
                            if (substr($value, 0, strlen('<outparam=?>')) == '<outparam=?>') {
                                $parts = explode('<outparam=?>', $value);
                                if (isset($parts[1]) && !empty($parts[1])) {
                                    $sqlvalue = $parts[1];
                                } else {
                                    $sqlvalue = '?';
                                }
                            } else {
                                $sqlvalue = is_string($value) ? '\''.$value.'\'' : $value;
                            }
                        }
                        $sql .= $sqlvalue.',';
                    }


                }


            }
            // remove the last comma
            $sql = substr($sql, 0, strlen($sql) - 1);


        }
        if ($p_type == 'procedure') {
            $sql .= $this->_callable_procedure_sep_string[1].';';

        } else {
            $sql .= $this->_callable_function_sep_string[1].';';
        }


        return $sql;
    }

    /**
     * @param string     $p_sp_name the name of the function or stored procedure
     * @param string     $p_type 'procedure' or 'function'
     * @param string     $p_type_return 'scalar' or 'records'
     * @param array|null $p_param_values array  of fields in the form [param_name=>value,..........]
     * @param array|null $p_param_descriptors this array have the form of :
     *
     *      [param_name=>[sqltype,direction,append_string,default_value],.......]
     *
     * This array of parameters can be obtained from get_callable_parameter_types() function.
     * If this values are already known use this parameter otherwise if its null this
     * method will call  get_callable_parameter_types() and obtain this array of param descriptors.
     *
     * @param array|null $p_exclude_casts array of field names that will be excluded from casts
     *
     * @return string with the callable string.
     */
    public function callable_string_extended(string $p_sp_name, string $p_type, string $p_type_return, ?array $p_param_values = null, ?array $p_param_descriptors = null, ?array $p_exclude_casts = null): string {

        if ($p_type == 'procedure') {
            $sql = $this->_callable_procedure_call_string.' '.$p_sp_name.$this->_callable_procedure_sep_string[0];

        } else {
            if ($p_type_return == 'scalar') {
                $sql = $this->_callable_function_call_string_scalar.' '.$p_sp_name.$this->_callable_function_sep_string[0];
            } else {
                $sql = $this->_callable_function_call_string.' '.$p_sp_name.$this->_callable_function_sep_string[0];
            }
        }

        // if the parameter descriptors are known in advance use them otherwise get the
        // descriptors.
        if ($p_param_descriptors != null) {
            $parameters = $p_param_descriptors;
        } else {
            $parameters = $this->get_callable_parameter_types($p_sp_name, $p_type);
        }

        if ($parameters !== null) {
            foreach ($parameters as $param_name => $parameter_descriptor) {
                // get the descriptors of the parameter
                // the form is : [param1=> [sqltype,direction,appendstring,default_value']
                // at least the first 3 are required
                $field_type = $parameter_descriptor[0];
                $appendstr = $parameter_descriptor[2] ?? '';
                $field_direction = $parameter_descriptor[1];

                // if not value in params value , try default if exist  otherwise null
                $value = $p_param_values[$param_name] ?? $parameter_descriptor[3] ?? null;

                $sqltype = $this->get_normalized_type($field_type);


                // normalize the value
                if ($field_direction == 'out') {
                    $value = '?';
                } else {
                    if ($value === null) {
                        $value = 'NULL';
                    } else {
                        switch ($sqltype) {
                            case 'string':
                                $value = '\''.$value.'\'';
                                break;

                            case 'boolean':
                                if ($value === 1 or $value === '1' or $value === 'true' or $value === 'TRUE' or $value === true or $value === 't') {
                                    $value = 'TRUE';
                                } else {
                                    $value = 'FALSE';
                                }
                                break;

                            case 'bit':
                                // sometimes is a bool (mssql server by example)
                                if ($value === 1 or $value === '1' or strtoupper($value) === 'TRUE' or $value === true or strtoupper($value) == 'T' or strtoupper($value) == 'YES') {
                                    $value = '\''.'1'.'\'';

                                } else {
                                    if ($value === 0 or $value === '0' or strtoupper($value) == 'FALSE' or $value === false or strtoupper($value) == 'F' or strtoupper($value) == 'NO') {

                                        $value = '\''.'0'.'\'';
                                    } else {
                                        $value = '\''.$value.'\'';

                                    }
                                }
                                break;
                            // numeric doesnt require special proces
                        }

                    }
                }


                // if not exclude form casting , check cast
                if ($p_exclude_casts === null || !in_array($param_name, $p_exclude_casts, true)) {
                    // if db support cast? and not is an out parameter
                    if ($this->_cast_on_callables && $field_direction != 'out') {
                        // do casts
                        $sql .= $this->cast_param($value, $field_type, $appendstr, false).',';
                    } else {
                        $sql .= $value.',';
                    }

                } else {
                    $sql .= $value.',';
                }

            }

            // remove the last comma, if parameters have elements.
            if (count($parameters) > 0 && strlen($sql) > 0) {
                $sql = substr($sql, 0, strlen($sql) - 1);
            }

            if ($p_type == 'procedure') {
                $sql .= $this->_callable_procedure_sep_string[1];

            } else {
                $sql .= $this->_callable_function_sep_string[1];
            }
        } else {
            throw new RuntimeException("callable_string_extended - sp/function $p_sp_name doesnt exist");
        }

        return $sql;
    }

    /**
     * Display an error message
     *
     * @param string $p_error the error message
     * @param string $p_type W-'warning' or E-'error' or 'e' soft error
     *
     */


    /***********************************************************************
     * Log stuff
     */

    public function log_error(string $p_error, string $p_type = 'W') {
        try {
            if ($p_type == 'E') {
                $error = $this->error();
                $msg = $p_error.' - '.$error['message'].'('.$error['code'].')';
            } else {
                $msg = $p_error;
            }

            if (self::$dblog_console) {
                echo $msg.PHP_EOL;
            } else {
                flcCommon::log_message(($p_type == 'E' || $p_type == 'e') ? 'error' : 'info', $msg);
            }

        } catch (Exception $ex) {
            if ($p_type !== 'E') {
                $msg = 'Imposible to log a message , check your log config ,'.$ex->getMessage();
            }

            if (self::$dblog_console) {
                echo $msg.PHP_EOL;
            }

            // is expected is handled befrore send results to the http server , for cli will be appear in screen.
            throw new RuntimeException($msg);
        }

    }
    /**
     * Determine if the error identify a duplicate key code
     * Need to be implemented in each driver to make sense.
     *
     * @param array $p_error database error obtained with error()
     * function
     *
     * @return bool
     */


    /****************************************************************
     * To identify specific errors
     */

    /**
     * Get the real parameter types for an specific function or stored procedure,
     * for use in construct in callable string when real field types will be used.
     *
     * The return array can have the format :
     *      [param_name=>[sqltype,direction,append_string,default_value],.......]
     * like in :
     *      [param1=> ['varchar','inout'],param2=>['int','in'],param3=>['varchar','in','(20)','defult value']]
     *
     * The first 3 are required allways , if not append string is required we expect an empty string.
     *
     * In this example :
     *      param_1 varchar(20)='i am a default value'  inout
     *
     * the string (20) is the append string and the string 'i am a default value' is the
     * default value.
     *
     *
     * @param string $p_callable_name the sp or function name in the current selected database
     * @param string $p_callable_type 'procedure' or 'functiom'
     *
     * @return array|null an associative array with parameter types (see explanation) or null if the fnction/sp not
     *     found.
     */
    protected abstract function get_callable_parameter_types(string $p_callable_name, string $p_callable_type): ?array;


    /**
     * Determine if the error identify a duplicate key error
     *
     * @param array $p_error database error obtained with error()
     * function
     *
     * @return bool
     */
    public function is_duplicate_key_error(array $p_error): bool {
        return false;
    }

    /**
     * Determine if the error identify a foreign key doesnt exist
     * Need to be implemented in each driver to make sense.
     *
     * @param array $p_error database error obtained with error()
     * function
     *
     * @return bool
     */
    public function is_foreign_key_error(array $p_error): bool {
        return false;
    }

    /****************************************************************
     * SQL types stuff
     */

    /**
     *
     * @param string $sql_real_type
     *
     * @return string can be numeric,string,boolean,bit
     */
    protected abstract function get_normalized_type(string $sql_real_type): string;
}



