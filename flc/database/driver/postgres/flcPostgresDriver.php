<?php

namespace flc\database\driver\postgres;


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

use flc\database\driver\flcDriver;
use flc\database\flcDbResult;
use flc\database\flcDbResultOutParams;
use flc\database\flcDbResults;
use Throwable;


/**
 * Postgres Driver Class
 *
 * This class extends the parent generic driver class: flc\database\driver\flcDriver
 * and is the specific one for postgresql.
 *
 * Important : Part of this class is a modified one of driver class from codeigniter
 * all credits for his authors.
 *
 * Supported version from 8.4 to current
 *
 *
 * @package        Database
 * @subpackage    Drivers
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
class flcPostgresDriver extends flcDriver {

    protected static array $_cast_conversion = [
        // boolean type
        'boolean' => ['boolean', 'b', 1],
        // text types
        'string' => ['character varying', 't'],
        'char' => ['character', 't'],
        'text' => ['text', 't'],
        'nstring' => ['character varying', 't'],
        'nchar' => ['char', 't'],
        'ntext' => ['text', 't'],
        // binary types
        'binary' => ['bytea', 't'],
        'varbinary' => ['bytea', 't'],
        'blob' => ['bytea', 't'],
        // enumerated types
        'enum' => ['enum', 't'], // need to append the cast name
        'set' => ['', 't'],
        // Numeric types
        'float' => ['float', 'n'],
        'double' => ['double precision', 'n'],
        'real' => ['real', 'n'],
        'decimal' => ['decimal', 'n'],
        'ufloat' => ['real', 'n'],
        'udouble' => ['double precision', 'n'],
        'udecimal' => ['double precision', 'n'],
        'bit' => ['bit', 'm'],
        'tinyint' => ['smallint', 'n'],
        'utinyint' => ['smallint', 'n'],
        'smallint' => ['smallint', 'n'],
        'usmallint' => ['integer', 'n'],
        'mediumint' => ['integer', 'n'],
        'umediumint' => ['integer', 'n'],
        'int' => ['integer', 'n'],
        'uint' => ['bigint', 'n'],
        'bigint' => ['bigint', 'n'],
        'ubigint' => ['numeric(20)', 'n'],
        'numeric' => ['numeric', 'n'],
        'unumeric' => ['numeric', 'n'],
        'money' => ['money', 'n'],
        // date / time types
        'date' => ['date', 't'],
        'datetime' => ['timestamp', 't'],
        'timestamp' => ['timestamp', 't'],
        'time' => ['time', 't'],
        'year' => ['', 'n'],
        // json / xml
        'json' => ['json', 't'],
        'jsonb' => ['jsonb', 't'],
        'xml' => ['xml', 't'],
        //  spatial data types
        'geometry' => ['GEOMETRY', 't'],
        'point' => ['point', 't'],
        'linestring' => ['line', 't'],
        'line' => ['line', 't'],
        'lseg' => ['lseg', 't'],
        'path' => ['path', 't'],
        'polygon' => ['polygon', 't'],
        'box' => ['box', 't'],
        'circle' => ['circle', 't'],
        'multipoint' => ['', 't'],
        'multilinestring' => ['', 't'],
        'geometrycollection' => ['', 't'],
        // Interval / ranges
        'int4range' => ['int4range', 't'],
        'int8range' => ['int8range', 't'],
        'numrange' => ['numrange', 't'],
        'tsrange' => ['tsrange', 't'],
        'tstzrange' => ['tstzrange', 't'],
        'daterange' => ['daterange', 't'],
        // FULL TEXT SEARCH
        'tsvector' => ['tsvector', 't'],
        'tsquery' => ['tsquery', 't'],
        //arrays
        'array' => ['', 't'], // to cast to array put the type of array and as append []
        // inet types
        'cidr' => ['cidr', 't'],
        'inet' => ['inet', 't'],
        'macaddr' => ['macaddr', 't'],

    ];


    /**
     * For scalar functions pgsql use the same syntax to get a set of records.
     *
     * @var string
     */
    protected string $_callable_function_call_string_scalar = 'select';

    /**
     * Class constructor
     *
     * @param array|null $p_options if null take defaults
     *
     * @return    void
     */
    public function __construct(?array $p_options = null) {
        parent::__construct($p_options);
    }

    /**
     * For postgres the dsn prototype will be : 'driver://username:password@hostname/database'
     *
     * @inheritDoc
     */
    public function initialize(?string $p_dsn, ?string $p_host, ?int $p_port, ?string $p_database, ?string $p_user, ?string $p_password, string $p_charset = 'utf8', string $p_collation = 'utf8_general_ci'): bool {
        // Extract dsn parts if well defined, if the values are on dsn they are taken otherwise extract
        // them from the parameters

        $query = "";
        if (($parsedDsn = @parse_url($p_dsn)) !== false) {
            $p_host = (isset($parsedDsn['host']) ? rawurldecode($parsedDsn['host']) : $p_host);
            $p_port = (isset($parsedDsn['port']) ? rawurldecode($parsedDsn['port']) : $p_port);
            $p_user = (isset($parsedDsn['user']) ? rawurldecode($parsedDsn['user']) : $p_user);
            $p_password = (isset($parsedDsn['pass']) ? rawurldecode($parsedDsn['pass']) : $p_password);
            $p_database = (isset($parsedDsn['database']) ? rawurldecode($parsedDsn['database']) : $p_database);
            $query = isset($parsedDsn['query']) ? rawurldecode($parsedDsn['query']) : "";

        }

        // generate dsn if its possible.

        // Set default if values not defined , generate the full dsn for postgres
        if (!isset($p_port)) {
            $p_port = '5432';
        }

        // Check values
        if (!isset($p_host) || !isset($p_user) || !isset($p_password) || !isset($p_database)) {
            return false;
        } else {
            $this->_dsn = 'postgresql://'.$p_user.':'.$p_password.'@'.$p_host.':'.$p_port.'/'.$p_database;
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

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Add support for the error verbosity , by default is terse.
     *
     * @inheritdoc
     */
    public function open(): bool {
        $ret = parent::open();
        if ($ret !== false) {
            pg_set_error_verbosity($this->_connId, PGSQL_ERRORS_TERSE);
        }
        return $ret;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function set_charset(string $p_charset): bool {
        // Check if open is called before
        if ($this->_connId) {
            return (pg_set_client_encoding($this->_connId, $p_charset) === 0);
        } else {
            return false;
        }
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _open() {
        // Never reuse a connection
        return pg_connect($this->_dsn, PGSQL_CONNECT_FORCE_NEW);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _close(): void {
        pg_close($this->_connId);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function get_db_driver(): string {
        return 'postgres';
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function connect(): bool {
        return $this->open();
    }

    // --------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function select_database(string $p_database): bool {
        return true;
    }

    // --------------------------------------------------------------------

    /**
     * The driver support ilke operator?
     * ilike is postgres supported.
     *
     * @return boolean false
     */
    public function is_ilike_supported(): bool {
        return true;
    }




    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function error(): array {
        return [
            'code' => 0,
            'message' => pg_last_error($this->_connId)
        ];
    }


    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _execute_qry(string $p_sqlquery) {
        return @pg_query($this->_connId, $p_sqlquery);
    }

    // --------------------------------------------------------------------

    /***************************************************************
     * Metadata
     */

    /**
     * @inheritdoc
     */
    protected function _list_columns_qry(string $p_table = ''): string {
        return 'SELECT "column_name"
			FROM "information_schema"."columns"
			WHERE LOWER("table_name") = '.$this->escape(strtolower($p_table));
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function primary_key(string $p_table): array {
        $pkeys = [];

        // PGSQL way to obtain the primary key field name
        $qry = 'SELECT a.attname
                    FROM   pg_index i
                    JOIN   pg_attribute a ON a.attrelid = i.indrelid
                                         AND a.attnum = ANY(i.indkey)
                    WHERE  i.indrelid = '.$this->escape(strtolower($p_table)).'::regclass
                    AND    i.indisprimary;';
        $RES = $this->execute_query($qry);

        if ($RES && $RES->num_rows() > 0) {
            $rows = $RES->result_array();
            foreach ($rows as $row) {
                $pkeys[] = $row['attname'];
            }
        }

        return $pkeys;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _column_data_qry(string $p_table, ?string $p_schema = null): string {
        return "select
                    col_name
                    ,col_type
                    ,case when col_length_1 isnull  then coalesce(col_length_2,col_length_1)  else col_length_1 end as col_length
                    ,col_scale
                    ,col_definition
                    ,case when col_is_pkey = true then 1 else 0 end as col_is_pkey
                    ,col_default
                    ,case when col_is_nullable = true then 1 else 0 end as col_is_nullable
                    from (SELECT a.attname                                                                   AS col_name
                               , a.atttypid::regtype                                                         as col_type
                               , substring(format_type(a.atttypid, a.atttypmod) from '\((\d+),(\d+)\)')::int AS col_length_1
                               , substring(format_type(a.atttypid, a.atttypmod) from ',(\d+)\)')::int        AS col_scale
                               , format_type(a.atttypid, a.atttypmod)                                        AS col_definition
                               , coalesce(p.indisprimary, FALSE)                                             AS col_is_pkey
                               , pg_get_expr(f.adbin, f.adrelid)                                             AS col_default
                               , case when a.attnotnull = true then false else true end                      AS col_is_nullable
                               , substring(format_type(a.atttypid, a.atttypmod) from '\((\d+)\)')::int       AS col_length_2
                    
                          FROM pg_attribute a
                                   LEFT JOIN pg_index p ON p.indrelid = a.attrelid AND a.attnum = ANY (p.indkey)
                                   LEFT JOIN pg_description d ON d.objoid = a.attrelid AND d.objsubid = a.attnum
                                   LEFT JOIN pg_attrdef f ON f.adrelid = a.attrelid AND f.adnum = a.attnum
                          WHERE a.attnum > 0
                            AND NOT a.attisdropped
                            AND a.attrelid = '".$p_schema.$p_table."'::regclass -- table may be schema-qualified
                          ORDER BY a.attnum) res";
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function column_data(string $p_table, string $p_schema = ''): ?array {
        if ($p_schema == '') {
            $schema = 'public';
        } else {
            $schema = $p_schema;
        }

        return parent::column_data($p_table, $p_schema);
    }

    /**
     * @inheritdoc
     */
    protected function _list_tables_qry(string $p_schema, string $p_table_constraints = ''): string {
        $sql = 'SELECT "table_name" FROM "information_schema"."tables" WHERE "table_schema" = \''.$p_schema."'";

        if (isset($p_table_constraints) && $p_table_constraints !== '') {
            $sql .= ' AND "table_name" LIKE \'%'.$p_table_constraints.'%\'';
        }

        return $sql;
    }

    // --------------------------------------------------------------------

    /*************************************************************
     * Helpers
     */

    /**
     * @inheritdoc
     *
     */
    public function cast_to_rowversion($p_value) {
        // in pgsql xmin is the rowversion field no transformation required
        return $p_value;
    }

    /**
     * @inheritdoc
     */
    protected function _version_qry(): string {
        return "SELECT unnest(string_to_array(current_setting('server_version'),' ')) as ver limit 1";
    }


    // --------------------------------------------------------------------

    /*************************************************************
     * Helpers
     */

    /**
     * @inheritdoc
     */
    protected function _escape_str(string $p_to_escape): string {
        return pg_escape_string($this->_connId, $p_to_escape);
    }

    // --------------------------------------------------------------------

    /**
     * @@inheritdoc
     */
    public function escape($p_to_escape) {
        if (version_compare(PHP_VERSION, '5.4.4', '>=') && (is_string($p_to_escape) or (is_object($p_to_escape) && method_exists($p_to_escape, '__toString')))) {
            return pg_escape_literal($this->_connId, $p_to_escape);
        } elseif (is_bool($p_to_escape)) {
            return ($p_to_escape) ? 'TRUE' : 'FALSE';
        }

        return parent::escape($p_to_escape);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function escape_identifiers($p_item) {

        if ($this->_escape_char === '' or empty($p_item) or in_array($p_item, $this->_reserved_identifiers)) {
            return $p_item;
        } elseif (is_array($p_item)) {
            foreach ($p_item as $key => $value) {
                $p_item[$key] = $this->escape_identifiers($value);
            }

            return $p_item;
        }

        return pg_escape_identifier($this->_connId, $p_item);
    }

    // --------------------------------------------------------------------

    /***********************************************************************
     * Transaction managing
     */

    /**
     * @inheritdoc
     */
    protected function _trans_begin(): bool {
        return (bool)pg_query($this->_connId, 'BEGIN');
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_commit(): bool {
        return (bool)pg_query($this->_connId, 'COMMIT');
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_rollback(string $p_savepoint = ''): bool {
        if ($p_savepoint === '') {
            return (bool)pg_query($this->_connId, 'ROLLBACK');
        } else {
            return (bool)pg_query($this->_connId, 'ROLLBACK TO '.$p_savepoint);
        }
    }

    // --------------------------------------------------------------------

    /***
     * @inheritdoc
     */
    protected function _trans_savepoint(string $p_savepoint): bool {
        return (bool)pg_query($this->_connId, 'SAVEPOINT '.$p_savepoint);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_remove_savepoint(string $p_savepoint): bool {
        return (bool)pg_query($this->_connId, 'RELEASE SAVEPOINT '.$p_savepoint);
    }

    // --------------------------------------------------------------------
    /*******************************************************
     * DB dependant methods
     */

    /**
     * @inheritdoc
     *
     * Important : postgres doesnt support limit or order by in update statementes (try use CTE, can be a solution)
     */
    protected function _update(string $p_table, array $p_values, string $p_where, int $p_limit = 0, ?string $p_orderby = null): string {
        $valstr = [];
        foreach ($p_values as $key => $val) {
            $valstr[] = $key.' = '.$val;
        }

        return "UPDATE $p_table SET ".implode(', ', $valstr)." WHERE $p_where";
    }


    // --------------------------------------------------------------------


    /***********************************************************************
     * Stored procedures and function support
     */

    /**
     * @inheritdoc
     */
    public function execute_function(string $p_fn_name, ?array $p_parameters = null, ?array $p_casts = null): ?flcDbResult {
        $is_scalar = false;

        // detect number of in parameters
        if ($p_parameters == null || count($p_parameters) == 0) {
            $numparams = 0;
        } else {
            $numparams = count($p_parameters);
        }

        // detect type function or procedure and if the return is refcursors, in postgres when we have return of refcursors
        // its imposible to have refcursors as inout or out parameters.
        // See we also search using number of input parameters because we can have 2 functions/procedures with the same name but
        // different parameters,
        // TODO: Verify same number of parameters but of different types.
        $res = $this->execute_query("SELECT proname,prokind,proretset FROM pg_proc p  WHERE pronargs = $numparams and proname = LOWER('$p_fn_name')");
        if ($res) {
            $row = $res->row_array(0);
            if ($row && $row['proretset'] == 'f') {
                $is_scalar = true;
            }
            $res->free_result();
        } else {
            return null;
        }

        if ($is_scalar) {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', 'scalar', $p_parameters, $p_casts);
        } else {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', 'records', $p_parameters, $p_casts);

        }
        echo $sqlfunc.PHP_EOL;


        return $this->execute_query($sqlfunc);

    }

    /**
     * @inheritdoc
     */
    public function execute_stored_procedure(string $p_fn_name, string $p_type, ?array $p_parameters = null, ?array $p_casts = null): ?flcDbResults {

        $params = [];
        $outparams_count = 0;
        $sqlpre = '';

        $is_outparams = false;
        $is_multiresult = false;
        $refcursors = [];

        // detect number of in parameters
        if ($p_parameters == null || count($p_parameters) == 0) {
            $numparams = 0;
        } else {
            $numparams = count($p_parameters);
        }

        // Analize if is a function/procedure and if has refcursor returns
        $type_and_ret = $this->_get_callable_type_and_ref_return($p_fn_name, $numparams);
        if ($type_and_ret !== null) {
            [$is_function, $refcursors_ret] = $type_and_ret;

        } else {
            return null;
        }

        if ($refcursors_ret) {
            $sqlpre = 'begin;';
        }

        // Parameter analisys
        if ($p_parameters && count($p_parameters) > 0) {
            for ($i = 0; $i < count($p_parameters); $i++) {
                // determine if parameter is an array or simple and get the value.
                if (is_array($p_parameters[$i])) {

                    // take the parameters descriptors.
                    $value = $p_parameters[$i][0];
                    $paramtype = $p_parameters[$i][1] ?? '';
                    $sqltype = $p_parameters[$i][2] ?? '';

                    if ($p_type == self::FLCDRIVER_PROCTYPE_SCALAR) {
                        $params[] = $value;
                    } elseif ($paramtype == self::FLCDRIVER_PARAMTYPE_OUT || $paramtype == self::FLCDRIVER_PARAMTYPE_INOUT) {
                        if ($sqltype !== 'refcursor') {
                            $is_outparams = true;
                            $outparams_count++;
                            $params[] = $value;

                        } else {
                            // refcursors
                            $params[] = $value;
                            $refcursors[] = $value;

                            $sqlpre = 'begin;';
                        }
                    } else {
                        $params[] = $value;
                    }


                } else {
                    $params[] = $p_parameters[$i];
                }
            }
        }

        // Type of resultset return
        $is_resultset = ($p_type == self::FLCDRIVER_PROCTYPE_RESULTSET);
        $is_multiresultset = ($p_type == self::FLCDRIVER_PROCTYPE_MULTIRESULTSET);

        // create sp store results
        require_once(dirname(__FILE__).'/../../flcDbResults.php');
        $results = new flcDbResults();


        // Get the callable string
        if ($is_function) {
            $sqlfunc = $this->callable_string($p_fn_name, 'function', ($is_resultset || $is_multiresultset) ? 'records' : 'scalar', $params, $p_casts);

        } else {
            $sqlfunc = $this->callable_string($p_fn_name, 'procedure', 'scalar', $params, $p_casts);

        }

        $sqlfunc = $sqlpre.$sqlfunc;
        //echo $sqlfunc.PHP_EOL;

        // Process and execute sp/function
        return $this->_process_callable($p_type, $sqlfunc, $results, $is_outparams, $is_resultset, $is_multiresultset, $refcursors, $refcursors_ret, $p_fn_name);

    }

    // --------------------------------------------------------------------


    /**
     * @inheritdoc
     */
    public function execute_callable(string $p_callable_name, string $p_type, ?array $p_params_values = null, ?array $p_exclude_casts = null): ?flcDbResults {
        $params = [];
        $outparams_count = 0;
        $sqlpre = '';

        $is_outparams = false;
        $is_multiresult = false;
        $refcursors = [];

        // Analize if is a function/procedure and if has refcursor returns
        $type_and_ret = $this->_get_callable_type_and_ref_return($p_callable_name);
        if ($type_and_ret !== null) {
            [$is_function, $refcursors_ret] = $type_and_ret;
        } else {
            return null;
        }

        if ($refcursors_ret) {
            $sqlpre = 'begin;';
        }

        // parameter analisys
        $parameter_descriptors = $this->get_callable_parameter_types($p_callable_name, !$is_function ? 'procedure' : 'function');
        if ($parameter_descriptors !== null) {
            foreach ($parameter_descriptors as $param_name => $parameter_descriptor) {
                // get the descriptors of the parameter
                // the form is : [param1=> ['varchar','INOUT'],param2=>['int','IN'],param3=>['varchar','IN','(20)']]
                $sqltype = $parameter_descriptor[0];
                $paramtype = $parameter_descriptor[1];
                $appendstr = $parameter_descriptor[2] ?? '';
                $value = $p_params_values[$param_name] ?? null;

                if ($p_type == self::FLCDRIVER_PROCTYPE_SCALAR) {
                    $params[$param_name] = $value;
                } elseif ($paramtype == 'out' || $paramtype == 'inout' || ($paramtype == 'in' && strtolower($sqltype) == 'refcursor')) {
                    if (strtolower($sqltype) !== 'refcursor') {
                        $is_outparams = true;
                        $outparams_count++;
                        $params[$param_name] = $value;

                    } else {
                        // refcursors
                        $params[$param_name] = $value;
                        $refcursors[] = $value;

                        $sqlpre = 'begin;';
                    }
                } else {
                    $params[$param_name] = $value;
                }

            }
        }

        // Type of resultset return
        $is_resultset = ($p_type == self::FLCDRIVER_PROCTYPE_RESULTSET);
        $is_multiresultset = ($p_type == self::FLCDRIVER_PROCTYPE_MULTIRESULTSET);

        // create sp store results
        require_once(dirname(__FILE__).'/../../flcDbResults.php');
        $results = new flcDbResults();

        // Get the callable string
        if ($is_function) {
            $sqlfunc = $this->callable_string_extended($p_callable_name, 'function', ($is_resultset || $is_multiresultset) ? 'records' : 'scalar', $params, $parameter_descriptors, $p_exclude_casts);

        } else {
            $sqlfunc = $this->callable_string_extended($p_callable_name, 'procedure', 'scalar', $params, $parameter_descriptors, $p_exclude_casts);

        }

        $sqlfunc = $sqlpre.$sqlfunc;
        //echo $sqlfunc.PHP_EOL;

        // Process and execute sp/function
        return $this->_process_callable($p_type, $sqlfunc, $results, $is_outparams, $is_resultset, $is_multiresultset, $refcursors, $refcursors_ret, $p_callable_name);


    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function get_callable_parameter_types(string $p_callable_name, string $p_callable_type): ?array {
        //  >= 8.4 pg_get_function_arguments
        // < 11 proisagg if false is function
        // >= 11 prokind 'f' function , 'p' procedure
        // use AND n.nspname = 'public' i f schema name required , here is in the current schema , no need to specify
        $version = $this->get_version();
        if (version_compare($version, '11', '>=')) {
            $sqlappend = 'AND prokind = ';
            if ($p_callable_type != 'procedure') {
                $sqlappend .= "'f'";
            } else {
                $sqlappend .= "'p'";

            }
        } else {
            // version < 11 doesnt have stored procedures
            $sqlappend = 'AND proisagg = FALSE';
        }

        //$sql = "SELECT pg_catalog.pg_get_function_arguments(p.oid) as args FROM pg_catalog.pg_proc p JOIN pg_catalog.pg_namespace n ON n.oid = p.pronamespace WHERE p.proname = '$p_callable_name' AND pg_catalog.pg_function_is_visible(p.oid) ".$sqlappend;

        $sql = "
                select
            a.parameter_name as p_name,
            case when typeall is null
                then types
            else
                typeall
            end as  p_type,
            coalesce(a.parameter_mode,'i') as p_mode,
            coalesce(b.optargdefaults,'') as p_default
        from(
                -- get all the parameters
                select
                        unnest(proargnames) as parameter_name,
                       unnest(proargmodes) as parameter_mode,
                        regtype(unnest(proallargtypes)) as typeall,
                        regtype(unnest(proargtypes)) as types
                FROM   pg_catalog.pg_proc
                WHERE  proname = '$p_callable_name'
                    AND pg_catalog.pg_function_is_visible(oid) $sqlappend
        ) a
        left join(
                     -- search for the parameters default and join
                     SELECT
                             unnest(proargnames[pronargs-pronargdefaults+1:pronargs] )optargnames,
                             unnest(string_to_array(pg_get_expr(proargdefaults, 0)::text,',')) optargdefaults
                     FROM   pg_catalog.pg_proc
                     WHERE  proname = '$p_callable_name'
        ) b on b.optargnames = a.parameter_name
        where parameter_mode in('i','o','b') or parameter_mode is null";


        // IMPORTANT . we dont expect length , precision , etc in parameters because in postgres is not allowed .
        // in the docs we found :
        // "It's illegal to specify a length, precision, scale, or other constraints on parameter declarations.
        // These constraints are inherited from the actual parameters that are used when the procedure or function is called."
        //
        // link to the doc :  https://www.enterprisedb.com/docs/epas/latest/epas_compat_spl/02_spl_programs/06_procedure_and_function_parameters/
        //
        $res = $this->execute_query($sql);
        if ($res) {
            $answers = [];

            $numrows = $res->num_rows();

            if ($numrows > 0) {
                $rows = $res->result_array();
                foreach ($rows as $row) {
                    $p_name = $row['p_name'];
                    $p_mode = $row['p_mode'];
                    $p_default = $row['p_default'];

                    if ($p_mode == 'i') {
                        $answers[$p_name] = [$row['p_type'], 'in', ''];
                    } elseif ($p_mode == 'b') {
                        $answers[$p_name] = [$row['p_type'], 'inout', ''];
                    } else {
                        $answers[$p_name] = [$row['p_type'], 'out', ''];
                    }

                    if (!empty($p_default)) {
                        $answers[$p_name][] = $p_default;
                    }

                }
            }

            $res->free_result();

        } else {
            $answers = null;
        }

        //print_r($answers);

        return $answers;

    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function insert_id(?string $p_table_name = null, ?string $p_column_name = null): int {
        if ($p_table_name && $p_column_name) {
            $sql = "SELECT currval(pg_get_serial_sequence('$p_table_name', '$p_column_name')) as ins_id";

            $res = $this->execute_query($sql);
            if ($res) {
                $insert_id = $res->row()->ins_id ?? 0;
                $res->free_result();

                return $insert_id;
            } else {
                $this->log_error('Can obtain insert id', 'E');

                return 0;
            }

        } else {
            $this->log_error('insert_id require the table and field name', 'e');

            return 0;
        }
    }



    // --------------------------------------------------------------------

    /**
     *
     * @inheritdoc
     */
    public function is_duplicate_key_error(array $p_error): bool {
        if (isset($p_error['message'])) {
            // for postgres pg_query only return messages
            if (stripos($p_error['message'], 'duplicate key value violates') !== false) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     *
     * @inheritdoc
     */
    public function is_foreign_key_error(array $p_error): bool {
        if (isset($p_error['message'])) {
            // for postgres pg_query only return messages
            if (stripos($p_error['message'], 'violates foreign key') !== false) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     *
     * Remove the context stuff from exception message.
     */
    protected function parse_exception_error_message(string $p_error_msg): string {
        $p_error_msg = str_replace('pg_query(): Query failed: ','',$p_error_msg);
        if (($pos = strpos($p_error_msg,'CONTEXT:  ')) !== false) {
            $p_error_msg = substr($p_error_msg,0,$pos);
        }

        return $p_error_msg;
    }


    /****************************************************************
     * SQL types stuff
     */

    /**
     *
     * @inheritdoc
     */
    protected function get_normalized_type(string $sql_real_type): string {
        switch (strtolower($sql_real_type)) {
            case 'boolean':
                return 'boolean';

            case 'character varying':
            case 'varchar':
            case 'character':
            case 'char':
            case 'text':
            case 'timestamp':
            case 'timestamptz':
            case 'timestamp with time zone';
            case 'timestamp without time zone';
            case 'date':
            case 'time':
            case 'point':
            case 'line':
            case 'lseg':
            case 'box':
            case 'path':
            case 'polygon':
            case 'circle':
            case 'inet':
            case 'cidr':
            case 'macaddr':
            case 'macaddr8':
            case 'bit':
            case 'tsvector':
            case 'tsquery':
            case 'uuid':
            case 'xml':
            case 'json':
            case 'jsonb':
            case 'array':
            case 'int4range':
            case 'int8range':
            case 'numrange':
            case 'tsrange':
            case 'tstzrange':
            case 'daterange':
            case 'oid':
            case '':
            case 'regclass':
            case 'regcollation':
            case 'regconfig':
            case 'regdictionary':
            case 'regnamespace':
            case 'regoper':
            case 'regoperator':
            case 'regproc':
            case 'regprocedure':
            case 'regrole':
            case 'regtype':
            case 'refcursor':
                return 'string';

            // otherwise , some kind of numeric
            default:
                return 'numeric';

        }
    }



    /***********************************************************
     * Private helpers
     */

    /**
     * Execute the final process of the sp/function execution.
     *
     * @param string $p_type
     * @param string $sqlfunc
     * @param flcDbResults $results
     * @param bool $is_outparams
     * @param bool $is_resultset
     * @param bool $is_multiresultset
     * @param array $refcursors
     * @param bool $refcursors_ret
     * @param string $p_fn_name
     *
     * @return flcDbResults|null
     *
     * @throws Throwable
     * @see flcPostgresDriver::execute_stored_procedure()
     * @see flcPostgresDriver::execute_callable()
     *
     */
    private function _process_callable(string $p_type, string $sqlfunc, flcDbResults $results, bool $is_outparams, bool $is_resultset, bool $is_multiresultset, array $refcursors, bool $refcursors_ret, string $p_fn_name): ?flcDbResults {
        // Process the stored procedurre
        //
        // If only a sp with a single return value?
        // generate the sql code and execute
        if (($p_type & self::FLCDRIVER_PROCTYPE_SCALAR) == self::FLCDRIVER_PROCTYPE_SCALAR) {

            $res = $this->execute_query($sqlfunc);
            $results->add_resultset_result($res);

        } // For stores procedures with output parameters o resultset or both
        elseif ($is_outparams || $is_resultset || $is_multiresultset) {

            $conn = $this->_connId;
            // execute
            $res = pg_query($conn, $sqlfunc);
            if ($res) {
                $refcursor_count = count($refcursors);
                // Have refcursors parameters?
                if ($refcursor_count > 0) {
                    // have output parameters ?
                    if ($is_outparams) {
                        // get the resultset first with the output parameters
                        $result_driver = $this->load_result_driver();
                        $result = new $result_driver($this, $res);


                        $results->add_resultset_result($result);

                    }

                    // process refcursor parameters
                    for ($i = 0; $i < $refcursor_count; $i++) {

                        if ($resrefs = $this->execute_query("fetch all in $refcursors[$i];")) {
                            $results->add_resultset_result($resrefs);
                        }

                    }
                    $resrefs = pg_query($conn, 'end;');

                } elseif ($refcursors_ret) {
                    // with refcursor return only, no in refcursors parameters
                    while ($row = pg_fetch_row($res)) {
                        pg_query($conn, "begin;");

                        if ($resrefs = $this->execute_query("fetch all in \"$row[0]\";")) {
                            $results->add_resultset_result($resrefs);
                        }
                    }
                    $resrefs = pg_query($conn, 'end;');

                } else {
                    // If a resulset type sp , get the results.
                    if ($is_resultset || $is_multiresultset) {

                        // Get the resulset or resultsets
                        $result_driver = $this->load_result_driver();
                        $result = new $result_driver($this, $res);


                        $results->add_resultset_result($result);


                    }
                }
                // we need to process output params?
                if ($is_outparams) {
                    // in the first resultset always we found the out params
                    $result = $results->get_resultset_result();
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
                        $results->resultset_free_result();

                    }
                }


            } else {
                $this->log_error("execute stored procedure  $p_fn_name fail", 'E');
                $results = null;
            }

        }

        return $results;
    }

    /**
     * Helper function to get the type of callable , function / sp and if return refcursors
     *
     * @param string $p_callable_name the name of the sp/function
     * @param int $numparams the expected number of parameters associated to the sp/function , if -1
     * search the callable without take into account the parameter number.
     *
     * @return bool[]|null if the functon doesnt exist or no more than one function with the
     * same name and/or parameters return null , otherwise and array of 2 boolean elements
     * the first indicate if it is a function or not and the second if the return if a refcursor or not.
     *
     * @throws Throwable
     */
    private function _get_callable_type_and_ref_return(string $p_callable_name, int $numparams = -1): ?array {
        $is_function = true;
        $refcursors_ret = false;

        $is_11 = false;
        $sqlappend = '';
        if ($numparams > 0) {
            $sqlappend = " and pronargs = $numparams";
        }

        //  >= 8.4 pg_get_function_identity_arguments
        // < 11 proisagg if false is function
        // >= 11 prokind 'f' function , 'p' procedure
        // use AND n.nspname = 'public' i f schema name required , here is in the current schema , no need to specify
        $version = $this->get_version();

        // detect type function or procedure and if the return is refcursors, in postgres when we have return of refcursors
        // its imposible to have refcursors as inout or out parameters.
        // See we also search using number of input parameters because we can have 2 functions/procedures with the same name but
        // different parameters,
        // TODO: Verify same number of parameters but of different types.
        if (version_compare($version, '11', '>=')) {
            $is_11 = true;
            $res = $this->execute_query("SELECT proname,prokind,pa.typname FROM pg_proc p INNER JOIN pg_type pa ON pa.oid = p.prorettype WHERE proname = LOWER('$p_callable_name')".$sqlappend);

        } else {
            $res = $this->execute_query("SELECT proname,proisagg,pa.typname FROM pg_proc p INNER JOIN pg_type pa ON pa.oid = p.prorettype WHERE pronargs = $numparams and  p.proisagg = FALSE".$sqlappend);
        }

        $answer = null;
        if ($res) {
            // more than on sp or function with the same name or same name/num parameters is not allowed
            if ($res->num_rows() == 1) {
                $row = $res->row_array(0);
                if ($is_11) {
                    if ($row['prokind'] == 'p') {
                        $is_function = false;
                    }

                }

                if (strtolower($row['typname']) === 'refcursor') {
                    $refcursors_ret = true;
                }
                $answer = [$is_function, $refcursors_ret];
            }
            $res->free_result();
        }

        return $answer;
    }

    // --------------------------------------------------------------------
}