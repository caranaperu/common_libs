<?php

namespace flc\database;

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
use flc\database\driver\flcDriver;

/**
 * Database Result Class
 *
 * This is the platform-independent result class.
 * This class will  be called directly if not specific adapter
 * class for the specific database exist.
 *
 * Important : This class is a modified one of db_result from codeigniter
 * all credits for his authors.
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
    protected int $num_rows = -1;

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * @param flcDriver $p_db_driver the flcDriver instance handling the results
     * @param resource  $p_result_id the result_id resource returned after execute a query on database
     */
    public function __construct(flcDriver $p_db_driver, $p_result_id) {
        $this->conn_id = $p_db_driver->get_connection();
        $this->result_id = $p_result_id;
    }

    // --------------------------------------------------------------------

    /**
     * Number of rows in the result set, if num_rows is already defined return
     * immediately otherwise search which array has answers, otherwise go to the db
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
     * @param string $p_type 'object', 'array' or a custom class name
     *
     * @return    array
     */
    public function result(string $p_type = 'object'): array {
        if ($p_type === 'array') {
            return $this->result_array();
        } elseif ($p_type === 'object') {
            return $this->result_object();
        } else {
            return $this->custom_result_object($p_type);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Custom query result, return an array of object of type $class_name.
     * for example :
     *
     * @param string $p_classname
     *
     * @return    array
     */
    public function custom_result_object(string $p_classname): array {
        if (isset($this->custom_result_object[$p_classname])) {
            return (array)$this->custom_result_object[$p_classname];
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

        // If data exists get data from the current array.
        if ($_data !== null) {
            for ($i = 0; $i < $c; $i++) {
                $this->custom_result_object[$p_classname][$i] = new $p_classname();

                foreach ($this->{$_data}[$i] as $key => $value) {
                    // only copy existent properties.
                    if (property_exists($this->custom_result_object[$p_classname][$i], $key)) {
                        $this->custom_result_object[$p_classname][$i]->$key = $value;
                    }
                }
            }

        } else {
            // read from database
            $this->data_seek();
            $this->custom_result_object[$p_classname] = [];

            while ($row = $this->_fetch_object($p_classname)) {
                $this->custom_result_object[$p_classname][] = $class = new $p_classname();
                foreach ($row as $key => $value) {
                    // only copy existent properties.
                    if (property_exists($class, $key)) {
                        $class->$key = $value;
                    }
                }
            }

            // Set the num rows
            $this->num_rows = count($this->custom_result_object[$p_classname]);
        }


        return $this->custom_result_object[$p_classname];
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
     * @param int    $p_nrecord
     * @param string $p_type 'object' or 'array'
     *
     * @return    object|array|null
     */
    public function row(int $p_nrecord = 0, string $p_type = 'object'): ?object {

        if ($p_type === 'object') {
            return $this->row_object($p_nrecord);
        } elseif ($p_type === 'array') {
            return $this->row_array($p_nrecord);
        } else {
            return $this->custom_row_object($p_nrecord, $p_type);
        }
    }



    // --------------------------------------------------------------------

    /**
     * Returns a single result row - custom object version
     * If $n exceed the max records or no records return null.
     *
     * @param int    $p_nrecord the number of record to return.
     * @param string $p_type 'array', 'object' or a custom class name
     *
     * @return    object|null
     */
    public function custom_row_object(int $p_nrecord, string $p_type): ?object {
        isset($this->custom_result_object[$p_type]) or $this->custom_result_object($p_type);

        if (count((array)$this->custom_result_object[$p_type]) == 0) {
            return null;
        }

        if (isset($this->custom_result_object[$p_type][$p_nrecord])) {
            $this->current_row = $p_nrecord;

            return $this->custom_result_object[$p_type][$this->current_row];
        } else {
            return null;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Returns a single result row - object version
     * If $n exceed the max records or no records return null.
     *
     * @param int $p_nrecord the number of record to return.
     *
     * @return    object|null
     */
    public function row_object(int $p_nrecord = 0): ?object {
        $result = $this->result_object();
        if (count($result) === 0) {
            return null;
        }

        if (isset($result[$p_nrecord])) {
            $this->current_row = $p_nrecord;

            return $result[$this->current_row];
        } else {
            return null;
        }

    }

    // --------------------------------------------------------------------

    /**
     * Returns a single result row - array version
     * If $n exceed the max records or no records return null.
     *
     * @param int $p_nrecord the number of record to return.
     *
     * @return    array
     */
    public function row_array(int $p_nrecord = 0): ?array {
        $result = $this->result_array();
        if (count($result) === 0 || !isset($result[$p_nrecord])) {
            return null;
        }

        $this->current_row = $p_nrecord;

        return $result[$this->current_row];
    }

    // --------------------------------------------------------------------

    /**
     * Returns the "first" row , null if no records.
     * Update the current record.
     *
     * @param string $p_type 'array', 'object' or a custom class name
     *
     * @return    object|array|null
     */
    public function first_row(string $p_type = 'object') {
        $result = $this->result($p_type);


        if (count($result) === 0) {
            return null;
        } else {
            $this->current_row = 0;

            return $result[0];
        }
    }

    // --------------------------------------------------------------------

    /**
     * Returns the "last" row, null if no records.
     * Update the current record.
     *
     * @param string $p_type 'array', 'object' or a custom class name
     *
     * @return    object|array|null
     */
    public function last_row(string $p_type = 'object') {
        $result = $this->result($p_type);

        $this->current_row = 0;

        $nrecords = count($result);
        if ($nrecords === 0) {
            return null;
        } else {
            $this->current_row = $nrecords - 1;

            return $result[$this->current_row];
        }

    }

    // --------------------------------------------------------------------

    /**
     * Returns the "next" row, null if no records.
     * Update the current record.
     *
     * @param string $p_type 'array' , 'object' or a custom class name
     *
     * @return    object|array|null
     */
    public function next_row(string $p_type = 'object') {
        $result = $this->result($p_type);
        if (count($result) === 0) {
            return null;
        }

        return isset($result[$this->current_row + 1]) ? $result[++$this->current_row] : null;
    }

    // --------------------------------------------------------------------

    /**
     * Returns the "previous" row, null if no records
     * Update the current record.
     *
     * @param string $p_type 'array' , 'object' or a custom class name
     *
     * @return    object|array|null
     */
    public function previous_row(string $p_type = 'object') {
        $result = $this->result($p_type);
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
     * @param string $p_type 'array', 'object' or a custom class name
     *
     * @return    object|array|null
     */
    public function unbuffered_row(string $p_type = 'object') {
        if ($p_type === 'array') {
            return $this->_fetch_assoc();
        } elseif ($p_type === 'object') {
            return $this->_fetch_object();
        }

        return $this->_fetch_object($p_type);
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
     * Field data of the results from a query
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
     * @param int $p_nrecord offset
     *
     * @return    bool true if all went ok
     */
    public function data_seek(int $p_nrecord = 0): bool {
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
     * @param string $p_classname
     *
     * @return    object
     */
    protected function _fetch_object(string $p_classname = 'stdClass'): ?object {
        return new $p_classname();
    }

    // --------------------------------------------------------------------

    /**
     * Affected Rows
     * Need to be override by each specific driver.
     *
     * For select querys use from the results  the num_rows() function
     * Warning : use inmediatly after execute_wuery/function etc because
     * in some databases this value its obtained from the connection and is global.
     * Allways get this value befre any other operation on the db.
     *
     * @return	int with the number of affected rows.
     */
    public function affected_rows() : int {
        return -1;
    }
}
