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

use framework\database\flcDbResult;
use stdClass;


/**
 * Mysql Result Class
 *
 * This class extends the parent result class: \framework\database\flcDbResult
 * and is the specific one for mysql.
 *
 * Important : This class is a modified one of msqli_result from codeigniter
 * all credits for his authors.
 * *
 * @package        Database
 * @subpackage    Drivers
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
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
            echo $bytesxchar.PHP_EOL;

            $retval[$i] = new stdClass();
            $retval[$i]->name = $field_data[$i]->name;
            $retval[$i]->type = flcMysqlResult::$_mysql_data_type[$field_data[$i]->type];
            $retval[$i]->length = $field_data[$i]->length / $bytesxchar;

            $retval[$i]->primary_key = ($field_data[$i]->flags & 2) == 2 ? 1 : 0;
            $retval[$i]->default = $field_data[$i]->def;
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function free_result(): void {
        if (is_object($this->result_id)) {
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
