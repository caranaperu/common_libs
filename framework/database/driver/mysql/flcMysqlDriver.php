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
use framework\database\flcDbResult;
use framework\database\flcDbResultOutParams;
use framework\database\flcDbResults;
use mysqli;
use stdClass;

include_once dirname(__FILE__).'/../flcDriver.php';

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
     * Hold the connection password
     *
     * @var string|null
     */
    protected ?string $_password = null;

    /**
     * Hold the connection socket
     *
     * @var string|null
     */
    private string    $_socket;

    /**
     * Hold the client flags
     *
     * @var int
     */
    private int       $_client_flags;

    /**
     * MySQLi object
     *
     * Has to be preserved without being assigned to $conn_id.
     *
     * @var mysqli|null $_mysqli
     */
    protected ?mysqli $_mysqli = null;



    /**
     * DELETE hack flag
     *
     * Whether to use the MySQL "delete hack" which allows the number
     * of affected rows to be shown. Uses a preg_replace when enabled,
     * adding a bit more processing to all queries.
     *
     * @var    bool
     */
    public bool $delete_hack = true;

    // --------------------------------------------------------------------

    protected static array $_cast_conversion = [
        // boolean type
        'boolean' => ['UNSIGNED', 'b', 1], // text types
        'string' => ['char', 't'], 'char' => ['CHAR', 't'], 'text' => ['CHAR', 't'], 'nstring' => ['NCHAR', 't'],
        'nchar' => ['NCHAR', 't'], 'ntext' => ['NCHAR', 't'], // binary types
        'binary' => ['BINARY', 't'], 'varbinary' => ['BINARY', 't'], 'blob' => ['BINARY', 't'], // enumerated types
        'enum' => ['ENUM', 't'], 'set' => ['SET', 't'], // Numeric types
        'float' => ['FLOAT', 'n'], 'double' => ['DOUBLE', 'n'], 'real' => ['REAL', 'n'], 'decimal' => ['DECIMAL', 'n'],
        'ufloat' => ['FLOAT', 'n'], // deprecated
        'udouble' => ['DOUBLE', 'n'], // deprecated
        'udecimal' => ['DECIMAL', 'n'], // deprecated
        'bit' => ['BIT', 'n'], 'tinyint' => ['SIGNED', 'n'], 'utinyint' => ['UNSIGNED', 'n'],
        'smallint' => ['SIGNED', 'n'], 'usmallint' => ['UNSIGNED', 'n'], 'mediumint' => ['SIGNED', 'n'],
        'umediumint' => ['UNSIGNED', 'n'], 'int' => ['SIGNED', 'n'], 'uint' => ['UNSIGNED', 'n'],
        'bigint' => ['SIGNED', 'n'], 'ubigint' => ['UNSIGNED', 'n'], 'numeric' => ['DECIMAL', 'n'],
        'unumeric' => ['DECIMAL', 'n'], 'money' => ['DECIMAL', 'n'], // date / time types
        'date' => ['DATE', 't'], 'datetime' => ['DATETIME', 't'], 'timestamp' => ['DATETIME', 't'],
        'time' => ['TIME', 't'], 'year' => ['', 'n'], // json / xml
        'json' => ['JSON', 't'], 'jsonb' => ['JSON', 't'], 'xml' => ['CHAR', 't'], // spatial data types
        'geometry' => ['GEOMETRY', 't'], 'point' => ['POINT', 't'], 'linestring' => ['LINESTRING', 't'],
        'line' => ['LINESTRING', 't'], 'lseg' => ['LINESTRING', 't'], 'path' => ['LINESTRING', 't'],
        'polygon' => ['POLYGON', 't'], 'box' => ['POLYGON', 't'], 'circle' => ['POLYGON', 't'],
        'multipoint' => ['MULTIPOINT', 't'], 'multilinestring' => ['MULTILINESTRING', 't'],
        'geometrycollection' => ['GEOMETRYCOLLECTION', 't'], // Interval / ranges
        'int4range' => ['', 't'], 'int8range' => ['', 't'], 'numrange' => ['', 't'], 'tsrange' => ['', 't'],
        'tstzrange' => ['', 't'], 'daterange' => ['', 't'], // FULL TEXT SEARCH
        'tsvector' => ['CHAR', 't'], 'tsquery' => ['CHAR', 't'], //arrays
        'array' => ['CHAR', 't'], // inet types
        'cidr' => ['CHAR(43)', 't'], 'inet' => ['CHAR(43)', 't'], 'macaddr' => ['CHAR(17)', 't'],

    ];

    // --------------------------------------------------------------------

    /**
     * For mysql the dsn prototype will be : 'driver://username:password@hostname/database'
     *
     * @inheritDoc
     */
    public function initialize(?string $p_dsn, ?string $p_host, ?int $p_port, ?string $p_database, ?string $p_user, ?string $p_password, string $p_charset = 'utf8', string $p_collation = 'utf8_general_ci'): bool {
        // For receive exceptions on errors
        mysqli_report(MYSQLI_REPORT_STRICT);


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

            $this->_password = $p_password;
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

                $this->display_error('Open failed , cant mantain an SSL connection','W');

                return null;
            }

            return $this->_mysqli;
        } else {
            return null;
        }


    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _close(): void {
        $this->_mysqli->close();
    }

    // --------------------------------------------------------------------

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
        return $this->open();
    }

    // --------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function select_database(string $p_database): bool {
        if ($p_database === '') {
            $p_database = $this->get_database();
        }

        if ($this->_connId->select_db($p_database)) {
            $this->_set_database($p_database);

            return true;
        }

        $this->display_error("Select database for $p_database fail",'E');

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function error(): array {
        if (!empty($this->_mysqli->connect_errno)) {
            return [
                'code' => $this->_mysqli->connect_errno, 'message' => $this->_mysqli->connect_error
            ];
        }

        return [
            'code' => $this->_mysqli->errno, 'message' => $this->_mysqli->error
        ];
    }

    // --------------------------------------------------------------------

    /**
     * Prep the query
     *
     * If needed, each database adapter can prep the query string
     *
     * @param string $p_sqlquery an SQL query
     *
     * @return    string
     */
    protected function _prep_query(string $p_sqlquery): string {
        // mysqli_affected_rows() returns 0 for "DELETE FROM TABLE" queries. This hack
        // modifies the query so that it a proper number of affected rows is returned.
        if ($this->delete_hack === true && preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $p_sqlquery)) {
            return trim($p_sqlquery).' WHERE 1=1';
        }

        return $p_sqlquery;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _execute_qry(string $p_sqlquery) {
        return $this->_connId->query($this->_prep_query($p_sqlquery));
    }

    // --------------------------------------------------------------------

    /***************************************************************
     * Metadata
     */

    /**
     * @inheritdoc
     */
    protected function _list_columns_qry(string $p_table = ''): string {
        return 'SELECT COLUMN_NAME as column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '.$this->escape($p_table).' AND table_schema = '.$this->escape($this->get_database());

    }

    /**
     * @inheritdoc
     */
    public function column_data(string $p_table, string $p_schema = ''): ?array {
        if ($p_schema == '') {
            $schema = $this->get_database();
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
            $retval[$i] = new stdClass();
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
            $dbname = $this->get_database();

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
        return $this->_connId->real_escape_string($p_to_escape);
    }

    /**
     * @inheritdoc
     */
    public function escape_identifiers($p_item) {

        if (is_array($p_item)) {
            foreach ($p_item as $key => $value) {
                $p_item[$key] = $this->escape_identifiers($value);
            }

            return $p_item;
        }

        return $this->_connId->real_escape_string($p_item);
    }

    /**
     * Important in mysql cant cast to bool , then left the value as is.
     *
     * @inheritdoc
     */
    public function cast_param(string $p_param, string $p_type, ?string $p_appendstr = null): string {
        if (strtolower($p_type) == 'boolean') {
            $conv = $p_param;
        } else {
            $conv = parent::cast_param($p_param, $p_type, $p_appendstr);
        }

        return $conv;
    }

    // --------------------------------------------------------------------

    /***********************************************************************
     * Transaction managing
     */

    /**
     * IMPORTANT: in mysql manual says :
     * "Beginning a transaction causes any pending transaction to be committed"
     *
     * That means if you try to rollback any operation after this begin transaction ,
     * will not be possible, because they are already committed.
     *
     * @inheritdoc
     */
    protected function _trans_begin(): bool {
        $this->_connId->autocommit(false);

        return version_compare(PHP_VERSION, '5.5', '>=') ? $this->_connId->begin_transaction() : $this->_connId->get_connection_id()->query('START TRANSACTION'); // can also be BEGIN or BEGIN WORK
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_commit(): bool {
        if ($this->_connId->commit()) {
            $this->_connId->autocommit(false);

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
            $ret = (bool)$this->_connId->rollback();
        } else {
            $ret = (bool)$this->_connId->query('ROLLBACK TO '.$p_savepoint);
        }

        if ($ret) {
            $this->_connId->autocommit(false);
        } else {
            $this->display_error("Rollback fail for $p_savepoint",'E');

        }

        return $ret;
    }

    // --------------------------------------------------------------------

    /***
     * @inheritdoc
     */
    protected function _trans_savepoint(string $p_savepoint): bool {
        return (bool)$this->_connId->savepoint($p_savepoint);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_remove_savepoint(string $p_savepoint): bool {
        return (bool)$this->_connId->release_savepoint($p_savepoint);
    }

    // --------------------------------------------------------------------

    /**********************************************************************
     * DB dependant methods
     */

    /***********************************************************************
     * Stored procedures and function support
     */

    /**
     * @inheritdoc
     */
    public function execute_function(string $p_fn_name, ?array $p_parameters = null, ?array $p_casts = null): ?flcDbResult {
        $sqlfunc = $this->callable_string($p_fn_name, 'function', 'scalar', $p_parameters, $p_casts);

        return $this->execute_query($sqlfunc);

    }

    /**
     * @inheritdoc
     */
    public function execute_stored_procedure(string $p_fn_name, string $p_type, ?array $p_parameters = null, ?array $p_casts = null): ?flcDbResults {

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

                    // Ignore refcursors for mantain compatability with pgsql
                    if ($sqltype == 'refcursor') {
                        continue;
                    }

                    // generate list of output paramenter
                    if ($paramtype == self::FLCDRIVER_PARAMTYPE_OUT || $paramtype == self::FLCDRIVER_PARAMTYPE_INOUT) {

                        $outparams_count++;

                        $sqlpre .= (strlen($sqlpre) > 0 ? ';' : '');
                        if ($paramtype == self::FLCDRIVER_PARAMTYPE_INOUT) {
                            $sqlpre .= 'set @p'.$i.'='.(is_string($value) ? '\''.$value.'\'' : $value);
                        }
                        $sqlpost .= (strlen($sqlpost) > 0 ? ',@p'.$i : 'select @p'.$i);

                        $params[] = '@p'.$i;
                    } else {
                        $params[] = (is_string($value) ? '\''.$value.'\'' : $value);
                    }


                } else {
                    $params[] = (is_string($p_parameters[$i]) ? '\''.$p_parameters[$i].'\'' : $p_parameters[$i]);
                }
            }
        }

        // create sp store results
        require_once(dirname(__FILE__).'/../../flcDbResults.php');
        $results = new flcDbResults();


        // Get the callable string
        $sqlfunc = $this->callable_string($p_fn_name, 'procedure', 'records', $params, $p_casts);
        if ($sqlpre) {
            $sqlfunc = $sqlpre.';'.$sqlfunc;

        }
        if ($sqlpost) {
            $sqlfunc = $sqlfunc.$sqlpost.';';

        }
        echo $sqlfunc;

        $mysqli = $this->_connId;
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
                    require_once(dirname(__FILE__).'/../../flcDbResultOutParams.php');
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
            $this->display_error("execute_stored_procedure fail for $sqlfunc",'E');
            return null;
        }


    }

}