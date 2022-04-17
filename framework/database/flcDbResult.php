<?php

namespace framework\database;

use framework\database\driver\flcDriver;

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

/**
 * Database Result Class
 *
 * This is the platform-independent result class.
 * This class will  be called directly if not specific adapter
 * class for the specific database exist.
 *
 * Important : This class is a modified one of db_result from codeigniter
 * all credits for his authors.
 *
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
 */
class flcDbResult {

    /**
     * Connection ID
     *
     * @var    resource|object
     */
    public $conn_id;

    /**
     * Result ID
     *
     * @var    resource|object
     */
    public $result_id;

    /**
     * Result Array
     *
     * @var    array[]
     */
    public array $result_array = [];

    /**
     * Result Object
     *
     * @var    object[]
     */
    public array $result_object = [];

    /**
     * Custom Result Object
     *
     * @var    object[]
     */
    public array $custom_result_object = [];

    /**
     * Current Row index
     *
     * @var    int
     */
    public int $current_row = 0;

    /**
     * Number of rows
     *
     * @var    int
     */
    public int $num_rows = -1;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param flcDriver $db_driver the flcDriver instance handling the results
     * @param resource  $result_id the result_id resource returned after execute a query on database
     */
    public function __construct(flcDriver $db_driver, $result_id) {
        $this->conn_id = $db_driver->get_connection()->get_connection_id();
        $this->result_id = $result_id;
    }

    // --------------------------------------------------------------------

    /**
     * Number of rows in the result set, if num_rows is already defined return
     * immediately otherwise search which array has answers.
     *
     * @return    int
     */
    public function num_rows(): int {
        if ($this->num_rows > 0) {
            return $this->num_rows;
        } elseif (count($this->result_array) > 0) {
            return $this->num_rows = count($this->result_array);
        } elseif (count($this->result_object) > 0) {
            return $this->num_rows = count($this->result_object);
        }

        return $this->num_rows = count($this->result_array());
    }

    // --------------------------------------------------------------------

    /**
     * Query result. Acts as a wrapper function for result_array,result_object or
     * custom_result_object depending of parameter.
     *
     * @param string $type 'object', 'array' or a custom class name
     *
     * @return    array
     */
    public function result(string $type = 'object'): array {
        if ($type === 'array') {
            return $this->result_array();
        } elseif ($type === 'object') {
            return $this->result_object();
        } else {
            return $this->custom_result_object($type);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Custom query result, return an array of object of type $class_name.
     *
     * @param string $class_name
     *
     * @return    array
     */
    public function custom_result_object(string $class_name): array {
        if (isset($this->custom_result_object[$class_name])) {
            return $this->custom_result_object[$class_name];
        } elseif (!$this->result_id or $this->num_rows === 0) {
            return [];
        }

        // Don't fetch the result set again if we already have it
        $_data = null;
        if (($c = count($this->result_array)) > 0) {
            $_data = 'result_array';
        } elseif (($c = count($this->result_object)) > 0) {
            $_data = 'result_object';
        }

        if ($_data !== null) {
            for ($i = 0; $i < $c; $i++) {
                $this->custom_result_object[$class_name][$i] = new $class_name();

                foreach ($this->{$_data}[$i] as $key => $value) {
                    $this->custom_result_object[$class_name][$i]->$key = $value;
                }
            }

        } else {
            // read from database
            $this->data_seek();
            $this->custom_result_object[$class_name] = [];

            while ($row = $this->_fetch_object($class_name)) {
                $this->custom_result_object[$class_name][] = $row;
            }

            // Set the num rows
            $this->num_rows = count($this->custom_result_object[$class_name]);
        }


        return $this->custom_result_object[$class_name];
    }

    // --------------------------------------------------------------------

    /**
     * Get the results as an array of objects , if already data in result_object or in result_array
     * take the data and return otherwise read from database.
     *
     * @return    array of objects
     */
    public function result_object(): array {
        if (count($this->result_object) > 0) {
            return $this->result_object;
        }

        // If num_rows is 0 then return empty array
        if ($this->num_rows === 0) {
            return [];
        }

        // If result_array has data now , parse each record to object and return.
        if (($c = count($this->result_array)) > 0) {
            for ($i = 0; $i < $c; $i++) {
                $this->result_object[$i] = (object)$this->result_array[$i];
            }

        } else {
            // Read from database
            $this->data_seek();
            while ($row = $this->_fetch_object()) {
                $this->result_object[] = $row;
            }
            // Set the num rows
            $this->num_rows = count($this->result_object);
        }

        return $this->result_object;
    }

    // --------------------------------------------------------------------

    /**
     * Get the results as an array of records , if already data in result_array or in result_object
     * take the data and return otherwise read from database.
     *
     * @return    array of records
     */
    public function &result_array(): array {
        // If already  the rows are fetched returns the previous result.
        if (count($this->result_array) > 0) {
            return $this->result_array;
        }

        // If num_rows is 0 then return empty array
        if ($this->num_rows == 0) {
            $this->result_array = [];
        } else {
            // If result_object has data now , parse each record to array and return.
            if (($c = count($this->result_object)) > 0) {
                for ($i = 0; $i < $c; $i++) {
                    $this->result_array[$i] = (array)$this->result_object[$i];
                }

            } else {
                // Read from database.
                $this->data_seek();
                while ($row = $this->_fetch_assoc()) {
                    $this->result_array[] = $row;
                }
                // Set the num rows
                $this->num_rows = count($this->result_array);
            }
        }

        return $this->result_array;
    }

    // --------------------------------------------------------------------

    /**
     * Row
     *
     * A wrapper method for get a row, depending on a type variable
     *
     * @param int    $n
     * @param string $type 'object' or 'array'
     *
     * @return    mixed|null
     */
    public function row(int $n = 0, string $type = 'object'): ?mixed {

        if ($type === 'object') {
            return $this->row_object($n);
        } elseif ($type === 'array') {
            return $this->row_array($n);
        } else {
            return $this->custom_row_object($n, $type);
        }
    }



    // --------------------------------------------------------------------

    /**
     * Returns a single result row - custom object version
     *
     * @param int    $n
     * @param string $type
     *
     * @return    object|null
     */
    public function custom_row_object(int $n, string $type): ?object {
        isset($this->custom_result_object[$type]) or $this->custom_result_object($type);

        if (count($this->custom_result_object[$type]) == 0) {
            return null;
        }

        if ($n !== $this->current_row && isset($this->custom_result_object[$type][$n])) {
            $this->current_row = $n;
        }

        return $this->custom_result_object[$type][$this->current_row];
    }

    // --------------------------------------------------------------------

    /**
     * Returns a single result row - object version
     *
     * @param int $n
     *
     * @return    object
     */
    public function row_object(int $n = 0): mixed {
        $result = $this->result_object();
        if (count($result) === 0) {
            return null;
        }

        if ($n !== $this->current_row && isset($result[$n])) {
            $this->current_row = $n;
        }

        return $result[$this->current_row];
    }

    // --------------------------------------------------------------------

    /**
     * Returns a single result row - array version
     *
     * @param int $n
     *
     * @return    array
     */
    public function row_array(int $n = 0): ?array {
        $result = $this->result_array();
        if (count($result) === 0) {
            return null;
        }

        if ($n !== $this->current_row && isset($result[$n])) {
            $this->current_row = $n;
        }

        return $result[$this->current_row];
    }

    // --------------------------------------------------------------------

    /**
     * Returns the "first" row
     *
     * @param string $type 'array', 'object' or a custom class name
     *
     * @return    mixed|null
     */
    public function first_row(string $type = 'object'): ?mixed {
        $result = $this->result($type);

        return (count($result) === 0) ? null : $result[0];
    }

    // --------------------------------------------------------------------

    /**
     * Returns the "last" row
     *
     * @param string $type 'array', 'object' or a custom class name
     *
     * @return    mixed|null
     */
    public function last_row(string $type = 'object'): ?mixed {
        $result = $this->result($type);

        return (count($result) === 0) ? null : $result[count($result) - 1];
    }

    // --------------------------------------------------------------------

    /**
     * Returns the "next" row
     *
     * @param string $type 'array' , 'object' or a custom class name
     *
     * @return    mixed|null
     */
    public function next_row(string $type = 'object'): ?mixed {
        $result = $this->result($type);
        if (count($result) === 0) {
            return null;
        }

        return isset($result[$this->current_row + 1]) ? $result[++$this->current_row] : null;
    }

    // --------------------------------------------------------------------

    /**
     * Returns the "previous" row
     *
     * @param string $type 'array' , 'object' or a custom class name
     *
     * @return    mixed|null
     */
    public function previous_row(string $type = 'object'): ?mixed {
        $result = $this->result($type);
        if (count($result) === 0) {
            return null;
        }

        if (isset($result[$this->current_row - 1])) {
            --$this->current_row;
        }

        return $result[$this->current_row];
    }

    // --------------------------------------------------------------------

    /**
     * Returns an unbuffered row and move pointer to next row
     *
     * @param string $type 'array', 'object' or a custom class name
     *
     * @return    mixed|false
     */
    public function unbuffered_row(string $type = 'object'): ?mixed {
        if ($type === 'array') {
            return $this->_fetch_assoc();
        } elseif ($type === 'object') {
            return $this->_fetch_object();
        }

        return $this->_fetch_object($type);
    }

    // --------------------------------------------------------------------

    /**
     * The following methods are normally overloaded by the identically named
     * methods in the platform-specific driver .
     * These functions are primarily here to prevent undefined function errors
     * when a cached result object is in use. They are not otherwise fully
     * operational due to the unavailability of the database resource IDs with
     * cached results.
     */

    // --------------------------------------------------------------------

    /**
     * Number of fields in the result set
     *
     * Overridden by driver result classes.
     *
     * @return    int
     */
    public function num_fields(): int {
        return 0;
    }

    // --------------------------------------------------------------------

    /**
     * Fetch Field Names
     *
     * Generates an array of column names.
     *
     * Overridden by driver result classes.
     *
     * @return    array
     */
    public function list_fields(): array {
        return [];
    }

    // --------------------------------------------------------------------

    /**
     * Field data
     *
     * Generates an array of objects containing field meta-data.
     *
     * Overridden by driver result classes.
     *
     * @return    array
     */
    public function field_data(): array {
        return [];
    }

    // --------------------------------------------------------------------

    /**
     * Free the result
     *
     * Overridden by driver result classes.
     *
     * @return    void
     */
    public function free_result(): void {
        $this->result_id = null;
    }

    // --------------------------------------------------------------------

    /**
     * Data Seek
     *
     * Moves the internal pointer to the desired offset. We call
     * this internally before fetching results to make sure the
     * result set starts at zero.
     *
     * Overridden by driver result classes.
     *
     * @param int $n
     *
     * @return    bool
     */
    public function data_seek(int $n = 0): bool {
        return FALSE;
    }

    // --------------------------------------------------------------------

    /**
     * Result - associative array
     *
     * Returns the result set as an array.
     *
     * Overridden by driver result classes.
     *
     * @return    array|bool , false if no more records otherwise an array with answers
     */
    protected function _fetch_assoc() {
        return [];
    }

    // --------------------------------------------------------------------

    /**
     * Result - object
     *
     * Returns the result set as an object.
     *
     * Overridden by driver result classes.
     *
     * @param string $class_name
     *
     * @return    object
     */
    protected function _fetch_object(string $class_name = 'stdClass'): ?object {
        return new $class_name();
    }

}
