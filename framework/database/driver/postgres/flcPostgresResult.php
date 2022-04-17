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

use framework\database\flcDbResult;


/**
 * Postgres Result Class
 *
 * This class extends the parent result class: \framework\database\flcDbResult
 * and is the specific one for postgresql.
 *
 * Important : This class is a modified one of postgre_result from codeigniter
 * all credits for his authors.
 * *
 * @package        Database
 * @subpackage    Drivers
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
class flcPostgresResult extends flcDbResult {

    /**
     * Number of rows in the result set
     *
     * @return    int
     */
    public function num_rows(): int {
        return ($this->num_rows > 0) ? $this->num_rows : $this->num_rows = pg_num_rows($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * Number of fields in the result set
     *
     * @return    int , -1 on error
     */
    public function num_fields(): int {
        return pg_num_fields($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch Field Names
     *
     * Generates an array of column names
     *
     * @return    array
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
     * Field data
     *
     * Generates an array of objects containing field meta-data
     *
     * @return    array
     */
    public function field_data(): array {
        $retval = [];
        for ($i = 0, $c = $this->num_fields(); $i < $c; $i++) {
            $retval[$i] = new stdClass();
            $retval[$i]->name = pg_field_name($this->result_id, $i);
            $retval[$i]->type = pg_field_type($this->result_id, $i);
            $retval[$i]->max_length = pg_field_size($this->result_id, $i);
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * Free the result
     *
     * @return    void
     */
    public function free_result(): void {
        if (is_resource($this->result_id)) {
            pg_free_result($this->result_id);
            $this->result_id = null;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Data Seek
     *
     * Moves the internal pointer to the desired offset. We call
     * this internally before fetching results to make sure the
     * result set starts at zero.
     *
     * @param int $n
     *
     * @return    bool true al went ok
     */
    public function data_seek(int $n = 0): bool {
        return pg_result_seek($this->result_id, $n);
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
     * Result - object
     *
     * Returns the result set as an object
     *
     * @param string $class_name
     *
     * @return    object|null
     */
    protected function _fetch_object(string $class_name = 'stdClass'): ?object {
        return pg_fetch_object($this->result_id, NULL, $class_name);
    }

}
