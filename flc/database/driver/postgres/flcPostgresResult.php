<?php

namespace flc\database\driver\postgres;

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

use flc\database\flcDbResult;
use stdClass;


/**
 * Postgres Result Class
 *
 * This class extends the parent result class: flc\database\flcDbResult
 * and is the specific one for postgresql.
 *
 * Important : This class is a modified one of postgre_result from codeigniter
 * all credits for his authors.
 */
class flcPostgresResult extends flcDbResult {

    /**
     * This array represents the mapping of internal data types in Postgres to standard types
     *
     * @var array|string[]
     */
    protected static array $_pg_to_std = [
        'bit' => 'bit',
        'bool' => 'boolean',
        'box' => 'box',
        'bpchar' => 'char',
        'bytea' => 'bytea',
        'cidr' => 'cidr',
        'circle' => 'circle',
        'date' => 'date',
        'daterange' => 'daterange',
        'float4' => 'real',
        'float8' => 'double precision',
        'inet' => 'inet',
        'int2' => 'smallint',
        'int4' => 'integer',
        'int4range' => 'int4range',
        'int8' => 'bigint',
        'int8range' => 'int8range',
        'interval' => 'interval',
        'json' => 'json',
        'lseg' => 'lseg',
        'macaddr' => 'macaddr',
        'money' => 'money',
        'numeric' => 'numeric',
        'numrange' => 'numrange',
        'path' => 'path',
        'point' => 'point',
        'polygon' => 'polygon',
        'text' => 'text',
        'time' => 'time',
        'timestamp' => 'timestamp',
        'timestamptz' => 'timestamp with time zone',
        'timetz' => 'time with time zone',
        'tsquery' => 'tsquery',
        'tsrange' => 'tsrange',
        'tsvector' => 'tsvector',
        'uuid' => 'uuid',
        'varbit' => 'bit varying',
        'varchar' => 'character varying',
        'xml' => 'xml',
    ];

    /**
     * @inheritdoc
     */
    public function num_rows(): int {
        return ($this->num_rows > 0) ? $this->num_rows : $this->num_rows = pg_num_rows($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function num_fields(): int {
        return pg_num_fields($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function list_fields(): array {
        $field_names = [];
        for ($i = 0, $c = $this->num_fields(); $i < $c; $i++) {
            $field_names[] = pg_field_name($this->result_id, $i);
        }

        return $field_names;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function field_data(): array {
        $retval = [];
        for ($i = 0, $c = $this->num_fields(); $i < $c; $i++) {
            $retval[$i] = new stdClass();
            $retval[$i]->col_name = pg_field_name($this->result_id, $i);
            $retval[$i]->col_type = flcPostgresResult::$_pg_to_std[pg_field_type($this->result_id, $i)];
            $retval[$i]->col_length = pg_field_size($this->result_id, $i);
            $retval[$i]->col_scale = 0;
            $retval[$i]->col_is_nullable = 0;
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function free_result(): void {
        if (is_resource($this->result_id)) {
            pg_free_result($this->result_id);
            $this->result_id = null;
        }
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function data_seek(int $p_nrecord = 0): bool {
        return pg_result_seek($this->result_id, $p_nrecord);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _fetch_assoc() {
        return pg_fetch_assoc($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _fetch_object(string $p_classname = 'stdClass'): ?object {
        $obj = pg_fetch_object($this->result_id, NULL, $p_classname);

        return is_object($obj) ? $obj : null;
    }


    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function affected_rows() : int {
        return pg_affected_rows($this->result_id);
    }

}