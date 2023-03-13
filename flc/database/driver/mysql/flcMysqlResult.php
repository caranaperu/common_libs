<?php

namespace flc\database\driver\mysql;

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
use mysqli_result;
use stdClass;


/**
 * Mysql Result Class
 *
 * This class extends the parent result class: \framework\database\flcDbResult
 * and is the specific one for mysql.
 *
 * Important : This class is a modified one of msqli_result from codeigniter
 * all credits for his authors.
 */
class flcMysqlResult extends flcDbResult {
    /**
     * Represents the relation between the id of the type and the type name
     * for mysql.
     *
     * @var array|string[]
     */
    private static array $_mysql_data_type = [
        MYSQLI_TYPE_DECIMAL => 'olddecimal',
        MYSQLI_TYPE_TINY => 'tinyint',
        MYSQLI_TYPE_SHORT => 'smallint',
        MYSQLI_TYPE_LONG => 'int',
        MYSQLI_TYPE_FLOAT => 'float',
        MYSQLI_TYPE_DOUBLE => 'double',
        MYSQLI_TYPE_TIMESTAMP => 'timestamp',
        MYSQLI_TYPE_LONGLONG => 'bigint',
        MYSQLI_TYPE_INT24 => 'int24',
        MYSQLI_TYPE_DATE => 'date',
        MYSQLI_TYPE_TIME => 'time',
        MYSQLI_TYPE_DATETIME => 'datetime',
        MYSQLI_TYPE_YEAR => 'year',
        MYSQLI_TYPE_NEWDATE => 'newdate',
        MYSQLI_TYPE_BIT => 'bit',
        MYSQLI_TYPE_BLOB => 'blob',
        MYSQLI_TYPE_VAR_STRING => 'varchar',
        MYSQLI_TYPE_STRING => 'char',
        MYSQLI_TYPE_NEWDECIMAL => 'decimal',
        MYSQLI_TYPE_SET => 'set',
        MYSQLI_TYPE_TINY_BLOB => 'tiny_blob',
        MYSQLI_TYPE_MEDIUM_BLOB => 'medium_blob',
        MYSQLI_TYPE_LONG_BLOB => 'long_blob',
        MYSQLI_TYPE_GEOMETRY => 'geometry'
    ];


    /**
     * This list is the result of take the first and fourth column of this
     * query:
     *
     * select id, cs.CHARACTER_SET_NAME, COLLATION_NAME, MAXLEN
     * from INFORMATION_SCHEMA.CHARACTER_SETS cs
     * INNER JOIN INFORMATION_SCHEMA.COLLATIONS co ON cs.CHARACTER_SET_NAME = co.CHARACTER_SET_NAME
     *
     * Rrepresents the number of bytes x character of an specific combination of chrset/collation
     * identified by the id field.
     *
     * @var array|int[]
     */
    private static array $_mysql_charset_bytesxchar = [];

    /**
     * @inheritdoc
     */
    public function num_rows(): int {

        return ($this->num_rows > 0) ? $this->num_rows : $this->num_rows = $this->result_id->num_rows;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function num_fields(): int {
        return $this->result_id->field_count;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function list_fields(): array {
        $field_names = [];
        $this->result_id->field_seek(0);
        while ($field = $this->result_id->fetch_field()) {
            $field_names[] = $field->name;
        }

        return $field_names;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function field_data(): array {
        $retval = [];
        $field_data = $this->result_id->fetch_fields();
        for ($i = 0, $c = count($field_data); $i < $c; $i++) {
            $bytesxchar = $this->_get_charset_bytes_per_character($field_data[$i]->charsetnr);

            $retval[$i] = new stdClass();
            $retval[$i]->col_name = $field_data[$i]->name;
            $retval[$i]->col_type = flcMysqlResult::$_mysql_data_type[$field_data[$i]->type];
            $retval[$i]->col_length = $field_data[$i]->length / $bytesxchar;
            $retval[$i]->col_scale = $field_data[$i]->decimals;

            $retval[$i]->col_is_nullable = ($field_data[$i]->flags & 1) == 1 ? 0 : 1;
            $retval[$i]->default = null;
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function free_result(): void {
        // mysqli return objects not resources , dont us is_resource here
        if ($this->result_id instanceof mysqli_result) {
            $this->result_id->free();
            $this->result_id = null;
        }
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function data_seek(int $p_nrecord = 0): bool {
        return $this->result_id->data_seek($p_nrecord);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _fetch_assoc() {
        return $this->result_id->fetch_assoc();
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _fetch_object(string $p_classname = 'stdClass'): ?object {
        $obj = $this->result_id->fetch_object($p_classname);

        return is_object($obj) ? $obj : null;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function affected_rows() : int {
        return mysqli_affected_rows($this->conn_id);
    }

    // --------------------------------------------------------------------
    /**
     * Mysql return an id of a charset/collation when get field data
     * and its necessary based on that information the number of bytes
     * per char to determine the real width of a field (the width in the
     * definition of the table).
     *
     * For example for varchar(50) , field_data returns a width of 150 ,
     * because in utf8 each char has 3 bytes.
     *
     * @param int $p_id ths id of chrset/collation
     *
     * @return int the number of bytes x char for an charset/collation id or
     * -1 if can get the data. (Never cant happen!!).
     *
     */
    protected function _get_charset_bytes_per_character(int $p_id): int {
        // If they are not cached , read from information schema
        if (count(flcMysqlResult::$_mysql_charset_bytesxchar) == 0) {
            $sqlquery = 'select id, cs.CHARACTER_SET_NAME, COLLATION_NAME, MAXLEN
                        from INFORMATION_SCHEMA.CHARACTER_SETS cs
                     INNER JOIN INFORMATION_SCHEMA.COLLATIONS co ON cs.CHARACTER_SET_NAME = co.CHARACTER_SET_NAME';
            $res = $this->conn_id->query($sqlquery);
            if ($res->num_rows > 0) {
                while ($row = $res->fetch_assoc()) {
                    flcMysqlResult::$_mysql_charset_bytesxchar[$row['id']] = $row['MAXLEN'];
                }
            } else {
                return -1;
            }
            $res->free();
            unset($res);
        }


        return flcMysqlResult::$_mysql_charset_bytesxchar[$p_id] ?? -1;
    }

}
