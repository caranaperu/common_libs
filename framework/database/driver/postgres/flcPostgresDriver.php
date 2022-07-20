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
        'boolean' => ['boolean', 'b',1],
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
    protected string       $_callable_function_call_string_scalar  = 'select * from';


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
        return $this->conn->open();
    }

    // --------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function select_database(string $p_database): bool {
        return TRUE;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function error() : array {
        return [
            'code' => 0,
            'message' => pg_last_error($this->conn->get_connection_id())
        ];
    }


    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _execute_qry(string $p_sqlquery) {
        return pg_query($this->conn->get_connection_id(), $p_sqlquery);
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
            $retval[$i] = new \stdClass();
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
        return pg_escape_string($this->conn->get_connection_id(), $p_to_escape);
    }

    // --------------------------------------------------------------------

    /**
     * @@inheritdoc
     */
    public function escape($p_to_escape) {
        if (version_compare(PHP_VERSION, '5.4.4', '>=') && (is_string($p_to_escape) or (is_object($p_to_escape) && method_exists($p_to_escape, '__toString')))) {
            return pg_escape_literal($this->conn->get_connection_id(), $p_to_escape);
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

        return pg_escape_identifier($this->conn->get_connection_id(), $p_item);
    }

    // --------------------------------------------------------------------

    /***********************************************************************
     * Transaction managing
     */

    /**
     * @inheritdoc
     */
    protected function _trans_begin(): bool {
        return (bool)pg_query($this->conn->get_connection_id(), 'BEGIN');
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_commit(): bool {
        return (bool)pg_query($this->conn->get_connection_id(), 'COMMIT');
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_rollback(string $p_savepoint = ''): bool {
        if ($p_savepoint === '') {
            return (bool)pg_query($this->conn->get_connection_id(), 'ROLLBACK');
        } else {
            return (bool)pg_query($this->conn->get_connection_id(), 'ROLLBACK TO '.$p_savepoint);
        }
    }

    // --------------------------------------------------------------------

    /***
     * @inheritdoc
     */
    protected function _trans_savepoint(string $p_savepoint): bool {
        return (bool)pg_query($this->conn->get_connection_id(), 'SAVEPOINT '.$p_savepoint);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _trans_remove_savepoint(string $p_savepoint): bool {
        return (bool)pg_query($this->conn->get_connection_id(), 'RELEASE SAVEPOINT '.$p_savepoint);
    }

    // --------------------------------------------------------------------

}