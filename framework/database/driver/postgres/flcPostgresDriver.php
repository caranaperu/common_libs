<?php

namespace framework\database\driver\postgres;


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
use stdClass;


/**
 * Postgres Driver Class
 *
 * This class extends the parent generic driver class: framework\database\driver\flcDriver
 * and is the specific one for postgresql.
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
        $this->rowversion_field = 'xmin';
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
     * @inheritdoc
     */
    protected function _set_charset(string $p_charset): bool {
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
        return true;
    }

    // --------------------------------------------------------------------

    /**
     * The driver support ilke operator?
     * ilike is postgres supported.
     *
     * @return boolean false
     */
    public function is_ilike_supported() : bool {
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
    public function primary_key(string $p_table): ?string {
        // PGSQL way to obtain the primary key field name
        $qry = 'SELECT a.attname
                    FROM   pg_index i
                    JOIN   pg_attribute a ON a.attrelid = i.indrelid
                                         AND a.attnum = ANY(i.indkey)
                    WHERE  i.indrelid = '.$this->escape(strtolower($p_table)).'::regclass
                    AND    i.indisprimary;';
        $RES = $this->execute_query($qry);

        if ($RES && $RES->num_rows() > 0) {
            return $RES->first_row()->attname;
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
            $schema = 'public';
        } else {
            $schema = $p_schema;
        }

        $sql = 'SELECT "column_name", "data_type", "character_maximum_length", "numeric_precision", "column_default"
			FROM "information_schema"."columns"
			WHERE table_schema = '.$this->escape(strtolower($schema)).' and LOWER("table_name") = '.$this->escape(strtolower($p_table));

        if (($query = $this->execute_query($sql)) === null) {
            return null;
        }
        $query = $query->result_object();

        $retval = [];
        for ($i = 0, $c = count($query); $i < $c; $i++) {
            $retval[$i] = new stdClass();
            $retval[$i]->name = $query[$i]->column_name;
            $retval[$i]->type = $query[$i]->data_type;
            $retval[$i]->max_length = ($query[$i]->character_maximum_length > 0) ? $query[$i]->character_maximum_length : $query[$i]->numeric_precision;
            $retval[$i]->default = $query[$i]->column_default;
        }

        return $retval;
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
        $is_function = true;
        $refcursors = [];

        $refcursors_ret = false;

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
        $res = $this->execute_query("SELECT proname,prokind,pa.typname FROM pg_proc p INNER JOIN pg_type pa ON pa.oid = p.prorettype WHERE pronargs = $numparams and proname = LOWER('$p_fn_name')");
        if ($res) {
            $row = $res->row_array(0);
            if ($row['prokind'] == 'p') {
                $is_function = false;
            }
            if ($row['typname'] === 'refcursor') {
                $refcursors_ret = true;
                $sqlpre = 'begin;';
            }
            $res->free_result();
        } else {
            return null;
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
                            $params[] = (is_string($value) ? '\''.$value.'\'' : $value);
                        } else {
                            // refcursors
                            $params[] = '\''.$value.'\'';
                            $refcursors[] = $value;

                            $sqlpre = 'begin;';
                        }
                    } else {
                        $params[] = (is_string($value) ? '\''.$value.'\'' : $value);
                    }


                } else {
                    $params[] = (is_string($p_parameters[$i]) ? '\''.$p_parameters[$i].'\'' : $p_parameters[$i]);
                }
            }
        }

        $is_resultset = ($p_type == self::FLCDRIVER_PROCTYPE_RESULTSET);
        $is_multiresultset = ($p_type == self::FLCDRIVER_PROCTYPE_MULTIRESULTSET);

        // create sp store results
        require_once(dirname(__FILE__).'/../../flcDbResults.php');
        $results = new flcDbResults();


        // Get the callable string
        if ($is_function) {
            if ($is_resultset || $is_multiresultset) {
                $sqlfunc = $this->callable_string($p_fn_name, 'function', 'records', $params, $p_casts);

            } else {
                $sqlfunc = $this->callable_string($p_fn_name, 'function', 'scalar', $params, $p_casts);
            }

        } else {
            $sqlfunc = $this->callable_string($p_fn_name, 'procedure', 'scalar', $params, $p_casts);

        }

        $sqlfunc = $sqlpre.$sqlfunc;
        echo $sqlfunc.PHP_EOL;


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

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function insert_id(?string $p_table_name = null, ?string $p_column_name = null): int {
        if ($p_table_name && $p_column_name) {
            $sql = "SELECT currval(pg_get_serial_sequence('$p_table_name', '$p_column_name')) as ins_id";

            $res = $this->execute_query($sql);
            if ($res) {
                $insert_id = $res->row()->insert_id ?? 0;
                $res->free_result();;

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
    public  function is_duplicate_key_error(array $p_error) : bool {
        if (isset($p_error['message'])) {
            // for postgres pg_query only return messages
            if (stripos($p_error['message'],'duplicate key value violates') !== false) {
                return true;
            }
        }
        return false;
    }

}