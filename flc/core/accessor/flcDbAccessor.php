<?php
/**
 * This file is part of Future Labs Code 1 framework.
 **
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 */

namespace flc\core\accessor;


use Exception;
use flc\core\accessor\constraints\flcConstraints;
use flc\core\model\flcBaseModel;
use flc\database\driver\flcDriver;
use flc\flcCommon;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Specific implementation of a persistence accessor for databases
 */
class flcDbAccessor extends flcPersistenceAccessor {

    static array $db_error_codes = [
        // Not database related
        'DB_NO_FIELDS_TO_EXECUTE' => 100,
        'DB_DELETE_NO_WHERE_CLAUSE' => 101,

        // dataBASE RELATED
        'DB_OPERATION_OK' => 100000,
        'DB_NOT_UNIQUE_ID' => 100001,
        'DB_RECORD_NOT_EXIST' => 100002,
        'DB_RECORD_EXIST' => 100003,
        'DB_RECORD_MODIFIED' => 100004,
        'DB_DUPLICATE_KEY' => 100005,
        'DB_FOREIGN_KEY_ERROR' => 100006,
        'DB_FAIL_OPEN' => 200000,
        'DB_INVALID_WHERE_FIELD' => 200001,
        'DB_OPERATION_FAIL' => 300000
    ];


    /**
     * The db driver to use with the acessor.
     * @var flcDriver
     */
    protected flcDriver $db;


    public function __construct(flcDriver $p_driver) {
        $this->db = $p_driver;
    }

    /*--------------------------------------------------------------*/

    /**
     * @inheritdoc
     */
    public function add(flcBaseModel &$p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): flcPersistenceAccessorAnswer {

        $answer = new flcPersistenceAccessorAnswer();
        $answer->set_success(false);

        $res = null;

        try {

            // do add
            $res = $this->db->execute_query($this->get_add_query($p_model, $p_suboperation));
            if ($res) {

                $id_field = $p_model->get_id_field();

                // try to get the las insert id , otherwise will read from the keys
                if (!empty($id_field)) {
                    $p_id_value = $this->db->insert_id($p_model->get_table_name(), $id_field);
                    if ($p_id_value > 0) {
                        $p_model->set_values([$id_field => $p_id_value]);
                    }
                }

                // Read to load all the values from the record in the model , important to update some automatic fields
                // or fields with defaults not currently in the received model..

                // doesnt take account of versiom id reading. (where clause , obvious)
                if ($p_model->is_rowversion_supported()) {
                    $p_model->{$p_model->get_rowversion_field()} = null;
                }
                $answer = $this->read($p_model, $p_suboperation, $p_constraints);

            } else {
                $answer->set_return_code($this->_process_db_error('add'));

            }

        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'add');
            if ($ret == self::$db_error_codes['DB_OPERATION_FAIL']) {
                $answer->set_exception($ex);
            } else {
                $answer->set_return_code($ret);
            }
        } finally {

            if ($res) {
                $res->free_result();
            }

        }


        // announce we have an error
        if ($answer->is_success()) {
            return $answer;
        } else {
            // mark transaction error
            $this->db->trans_mark_dirty();
        }

        return $answer;
    }

    /*--------------------------------------------------------------*/

    /**
     * @inheritdoc
     */
    public function update(flcBaseModel &$p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): flcPersistenceAccessorAnswer {

        $answer = new flcPersistenceAccessorAnswer();
        $answer->set_success(false);

        $res = null;

        try {

            $rowversion_support = $p_model->is_rowversion_supported();

            $res = $this->db->execute_query($this->get_update_query($p_model, $p_suboperation));

            if ($res) {

                $affected_rows = $res->affected_rows();

                if ($affected_rows <= 0) {
                    // Probably nothing in the record is change or a real error ocurred
                    // check.
                    $error = $this->db->error();

                    if (!$this->is_db_error($error)) {
                        // if simply nothing was changed , then no error , next step
                        // reread to verify changes.

                        // force to read without rowversion
                        if ($p_model->is_rowversion_supported()) {
                            $p_model->{$p_model->get_rowversion_field()} = null;
                        }

                        $answer = $this->read($p_model, $p_suboperation);

                        if ($rowversion_support) {
                            // its deleted or modified?
                            // its delete will return self::$db_error_codes['DB_RECORD_NOT_EXIST']

                            if ($answer->is_success()) {
                                $answer->set_success(false);
                                $answer->set_return_code(self::$db_error_codes['DB_RECORD_MODIFIED']);
                            }

                        }
                    } else {
                        $answer->set_return_code($this->_process_db_error('update'));
                    }

                } else {
                    // in an add record rowversion field value is allways null , because is not known
                    $answer = $this->read($p_model, $p_suboperation, $p_constraints);
                }


            } else {
                $answer->set_return_code($this->_process_db_error('update'));
            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'update');
            if ($ret == self::$db_error_codes['DB_OPERATION_FAIL']) {
                $answer->set_exception($ex);
            } else {
                $answer->set_return_code($ret);
            }
        } finally {

            if ($res) {
                $res->free_result();
            }
        }

        if ($answer->is_success()) {
            return $answer;
        } else {
            // mark transaction error
            $this->db->trans_mark_dirty();
        }


        return $answer;
    }

    /*--------------------------------------------------------------*/

    /**
     * @inheritdoc
     */
    public function delete(flcBaseModel &$p_model, bool $p_verify_deleted_check = true): flcPersistenceAccessorAnswer {

        $answer = new flcPersistenceAccessorAnswer();
        $answer->set_success(false);

        $res = null;

        try {
            $res = $this->db->execute_query($this->get_delete_query($p_model));

            if ($res) {

                if ($p_verify_deleted_check) {
                    if ($res->affected_rows() <= 0) {
                        // Is modified or not exist.
                        // force read without using rowversion field
                        if ($p_model->is_rowversion_supported()) {
                            $p_model->{$p_model->get_rowversion_field()} = null;
                        }

                        $answer = $this->read($p_model);
                        // if we can read without taking account of the rowversion field , means is modificed
                        // otherwise doesnt exist.
                        if ($answer->is_success()) {
                            $answer->set_success(false);
                            $answer->set_return_code(self::$db_error_codes['DB_RECORD_MODIFIED']);
                        } else {
                            if ($answer->get_exception() == null) {
                                $answer->set_return_code(self::$db_error_codes['DB_RECORD_NOT_EXIST']);
                            }
                        }
                    } else {
                        $answer->set_success(true);
                    }
                } else {
                    $answer->set_success(true);
                }
            } else {
                $answer->set_return_code($this->_process_db_error('delete'));
            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'delete');
            if ($ret == self::$db_error_codes['DB_OPERATION_FAIL']) {
                $answer->set_exception($ex);
            } else {
                $answer->set_return_code($ret);
            }
        } finally {
            if ($res) {
                $res->free_result();
            }

        }

        if ($answer->is_success()) {
            $answer->set_result_array([]);
        } else {
            // mark transaction error
            $this->db->trans_mark_dirty();
        }

        return $answer;
    }

    /*--------------------------------------------------------------*/

    /**
     * @inheritdoc
     */
    public function delete_full(flcBaseModel &$p_model, flcConstraints $p_constraints): flcPersistenceAccessorAnswer {
        $answer = new flcPersistenceAccessorAnswer();
        $answer->set_success(false);

        $res = null;

        try {
            $res = $this->db->execute_query($this->get_delete_query_full($p_model, $p_constraints));

            if ($res) {

                if ($res->affected_rows() <= 0) {
                    $answer->set_return_code(self::$db_error_codes['DB_RECORD_NOT_EXIST']);
                } else {
                    $answer->set_success(true);

                }
            } else {
                $answer->set_return_code($this->_process_db_error('delete full'));

            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'delete');
            if ($ret == self::$db_error_codes['DB_OPERATION_FAIL']) {
                $answer->set_exception($ex);
            } else {
                $answer->set_return_code($ret);
            }
        } finally {

            if ($res) {
                $res->free_result();
            }
        }

        if ($answer->is_success()) {
            $answer->set_result_array([]);
        } else {
            // mark transaction error
            $this->db->trans_mark_dirty();
        }


        return $answer;
    }

    /*--------------------------------------------------------------*/

    /**
     * @inheritdoc
     */
    public function read(flcBaseModel &$p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): flcPersistenceAccessorAnswer {

        $answer = new flcPersistenceAccessorAnswer();
        $answer->set_success(false);

        $res = null;
        try {

            $res = $this->db->execute_query($this->get_read_query($p_model, $p_suboperation, $p_constraints));

            if ($res) {
                if ($res->num_rows() == 0) {
                    $answer->set_return_code(self::$db_error_codes['DB_RECORD_NOT_EXIST']);

                } else {
                    $p_model->set_values($res->row_array());
                    // remove from the answer model not allowed in read operations
                    $valid_operations_field = $p_model->get_fields_operations();
                    if ($valid_operations_field && count($valid_operations_field) > 0) {
                        foreach ($valid_operations_field as $field => $operations) {
                            if (strpos($operations,'r') === false) {
                                $p_model->unset_field($field);
                            }
                        }
                    }
                    $answer->set_success(true);
                }

            } else {
                $answer->set_return_code($this->_process_db_error('read'));

            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'read');
            if ($ret == self::$db_error_codes['DB_OPERATION_FAIL']) {
                $answer->set_exception($ex);
            } else {
                $answer->set_return_code($ret);
            }
        } finally {
            if ($res) {
                $res->free_result();
            }

        }

        if ($answer->is_success()) {
            $results[] = $p_model->get_all_fields('rc');
            $answer->set_result_array($results);
        } else {
            // mark transaction error
            $this->db->trans_mark_dirty();
        }


        return $answer;
    }

    /*--------------------------------------------------------------*/


    /*--------------------------------------------------------------*/

    /**
     * @inheritdoc
     */
    public function fetch(flcBaseModel $p_model, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null): flcPersistenceAccessorAnswer {
        $answer = new flcPersistenceAccessorAnswer();
        $answer->set_success(false);

        try {
            $sql = $this->get_fetch_query($p_model, $p_constraints, $p_suboperation);
            $res = $this->db->execute_query($sql);
            if ($res) {
                $answer->set_result_array($res->result_array());
                $answer->set_success(true);
            } else {
                $answer->set_return_code($this->_process_db_error('fetch full'));

            }
        } catch (Throwable $ex) {

            $ret = $this->_process_exception($ex, 'fetch full');
            if ($ret == self::$db_error_codes['DB_OPERATION_FAIL']) {
                $answer->set_exception($ex);
            } else {
                $answer->set_return_code($ret);
            }
        }

        // announce we have an error
        if (!$answer->is_success()) {
            $this->db->trans_mark_dirty();
        }

        return $answer;
    }

    /*--------------------------------------------------------------*/


    /**
     * Return the query required to add one instance of the model.
     *
     * @param flcBaseModel $p_model the model values requiered for creATE THE QUERY.
     * @param string|null  $p_suboperation optional user defined suboperation.
     *
     * @return string
     * @throws Exception
     */
    protected function get_add_query(flcBaseModel $p_model, ?string $p_suboperation = null): string {
        // setup add fields
        //
        $fields = $p_model->get_fields();
        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to read', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // search on field operations to remove not supported in the operation
        foreach ($p_model->get_fields_operations() as $ofield => $soperation) {
            // if it is a computed field? then skipL
            if (strpos($soperation, 'c') !== false) {
                continue;
            }

            // if a field with operations defined exist in the field list , but the operation
            // is not suppported remove element.
            if (array_key_exists($ofield, $fields)) {
                if (strpos($soperation, 'a') === false) {
                    unset($fields[$ofield]);
                }
            }
        }


        // verify key fields are defined or at least an id
        $no_all_keys = false;
        $ids = $p_model->get_key_fields();
        if (!isset($ids) || count($ids) == 0) {
            $no_all_keys = true;
        } else {
            foreach ($ids as $id) {
                if (!array_key_exists($id, $fields) || empty($fields[$id])) {
                    $no_all_keys = true;
                }
            }
        }

        if ($no_all_keys) {
            $id_field = $p_model->get_id_field();
            if (empty($id_field)) {
                throw new InvalidArgumentException('Not key fiels or ids are defined for add a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

            }

        }

        $id_field = $p_model->get_id_field();
        $rowid_field = $p_model->get_rowversion_field();


        $sql = "INSERT INTO {$p_model->get_table_name()} (";
        foreach (array_keys($fields) as $field) {
            // the id and row id field never will be passed in the list to add , are automatic fields
            if ($field !== $id_field && $field != $rowid_field ) {
                $sql .= $field.',';
            }
        }
        $sql = rtrim($sql,',').') VALUES (';

        foreach ($fields as $field => $value) {
            // if unique id , or autoincrement cant add to add query.
            if ($field !== $id_field && $field != $rowid_field ) {
                $type = $p_model->get_field_type($field) ?? '';

                $sql .= $this->get_normalized_field_value($value, $type).',';

            }
        }

        //$sql = substr($sql, 0, strrpos($s
        $sql = rtrim($sql,',');
        $sql .= ')';

        $this->log_error($sql, 'I');


        return $sql;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the query required to update one instance of the model.
     *
     * @param flcBaseModel        $p_model the model values requiered for creATE THE QUERY.
     * @param string|null         $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints the constraints required to update (optional).
     *
     * @return string the query string
     * @throws Exception
     */
    protected function get_update_query(flcBaseModel $p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): string {
        // setup update fields
        //
        $fields = $p_model->get_fields();
        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to read', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // search on field operations to remove not supported in the operation
        foreach ($p_model->get_fields_operations() as $ofield => $soperation) {
            // if it is a computed field? then skip
            if (strpos($soperation, 'c') !== false) {
                continue;
            }

            // if a field with operations defined exist in the field list , but the operation
            // is not suppported remove element.
            if (array_key_exists($ofield, $fields)) {
                if (strpos($soperation, 'u') === false) {
                    unset($fields[$ofield]);
                }
            }
        }

        // verify key fields are defined or at least an id
        $no_all_keys = false;
        $keys = $p_model->get_key_fields();
        if (!isset($keys) || count($keys) == 0) {
            $no_all_keys = true;
        } else {
            foreach ($keys as $id) {
                if (!array_key_exists($id, $fields) || empty($fields[$id])) {
                    $no_all_keys = true;
                }
            }
        }

        if ($no_all_keys) {
            $id_field = $p_model->get_id_field();
            if (empty($id_field)) {
                throw new InvalidArgumentException('Not key fiels or ids are defined for read a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

            }

        }

        $id_field = $p_model->get_id_field();
        $rowversion_field = $p_model->get_rowversion_field();

        // update list
        $sql = 'update '.$p_model->get_table_name().' set ';
        foreach ($fields as $field => $value) {
            // the id field and the rowversion field never will be updated, because they are automatic
            if ($field !== $id_field && $field !== $rowversion_field) {
                $type = $p_model->get_field_type($field) ?? '';
                $sql .= $field.'='.$this->get_normalized_field_value($value, $type).',';
            }
        }

        // where list
        $sql = substr($sql, 0, strrpos($sql, ','));
        $sql .= ' where ';


        $where_fields = null;
        if ($p_constraints) {
            $where_fields = $p_constraints->get_where_fields();
        }

        // if where fields exist in constraints use them.
        if ($where_fields && count($where_fields) > 0) {
            foreach ($where_fields as $key => $value) {
                $type = $p_model->get_field_type($key) ?? '';

                $sql .= $key.'='.$this->get_normalized_field_value($value, $type).' and ';
            }

        } else {
            // if no key fields use the id.
            if (count($keys) > 0) {
                foreach ($keys as $key) {
                    $type = $p_model->get_field_type($key) ?? '';

                    $sql .= $key.'='.$this->get_normalized_field_value($fields[$key], $type).' and ';
                }
            } else {
                $sql .= $p_model->get_id_field().'='.$fields[$p_model->get_id_field()].' and ';

            }

        }


        // add rowversion field to where
        // check if row version is supported
        if ($p_model->is_rowversion_supported()) {
            $rowversion_field = $p_model->get_rowversion_field();

            $value = $p_model->{$rowversion_field};
            // If this value is null , means not use on the query
            if ($value !== null) {
                $type = $p_model->get_field_type($rowversion_field) ?? '';

                $sql .= $rowversion_field.' = '.$this->get_normalized_field_value($value, $type).' and '; // trailing and is on purpose
            }
        }
        $sql = substr($sql, 0, strrpos($sql, ' and '));


        $this->log_error($sql, 'I');

        return $sql;

    }

    /*--------------------------------------------------------------*/

    /**
     * Return the query required to delete one instance of the model and only one based in the key fields if exist or
     * the uniqye id if no keys defined.
     *
     * @param flcBaseModel $p_model the model values requiered for delete.
     *
     * @return string with the delete query
     * @throws Exception
     */
    protected function get_delete_query(flcBaseModel $p_model): string {
        $fields = $p_model->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to delete', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // verify key fields are defined or at least an id
        $no_all_keys = false;
        $keys = $p_model->get_key_fields();
        if (!isset($keys) || count($keys) == 0) {
            $no_all_keys = true;
        } else {
            foreach ($keys as $id) {
                if (!array_key_exists($id, $fields) || empty($fields[$id])) {
                    $no_all_keys = true;
                }
            }
        }

        if ($no_all_keys) {
            $id_field = $p_model->get_id_field();
            if (empty($id_field)) {
                throw new InvalidArgumentException('Not key fiels or ids are defined for delete a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

            }

        }

        $sql = "delete from {$p_model->get_table_name()} where ";

        // if no key fields use the id.
        if (count($keys) > 0) {
            foreach ($keys as $key) {
                $type = $p_model->get_field_type($key) ?? '';
                $sql .= $key.'='.$this->get_normalized_field_value($fields[$key], $type).' and ';
            }
        } else {
            $sql .= $p_model->get_id_field().'='.$fields[$p_model->get_id_field()].' and ';

        }

        // add rowversion field to where
        // check if row version is supported
        if ($p_model->is_rowversion_supported()) {
            $rowversion_field = $p_model->get_rowversion_field();

            $value = $p_model->{$rowversion_field};
            // If this value is null , means not use on the query
            if ($value !== null) {
                $type = $p_model->get_field_type($rowversion_field) ?? '';

                $sql .= $rowversion_field.' = '.$this->get_normalized_field_value($value, $type).' and '; // trailing and is on purpose
            }
        }

        $sql = substr($sql, 0, strrpos($sql, ' and '));

        $this->log_error($sql, 'I');


        return $sql;
    }

    /*--------------------------------------------------------------*/


    /**
     * Return the query required to delete one or more instances of the model based on the constraints.
     *
     * @param flcBaseModel        $p_model the model values requiered for create the query.
     * @param flcConstraints|null $p_constraints the constraints required to delete (optional).
     *
     * @return string with the delete query
     * @throws Exception
     */
    public function get_delete_query_full(flcBaseModel $p_model, flcConstraints $p_constraints): string {
        $fields = $p_model->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to delete', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        $sql = "delete from {$p_model->get_table_name()} ";
        $sqlwhere = $this->where_clause($p_model->get_table_name(), $fields, $p_model->get_field_types(), $p_constraints);

        if ($sqlwhere != '') {
            $sql .= $sqlwhere;
        } else {
            // Not delete without where !!!!!!
            throw new InvalidArgumentException('Not where clause defined to delete, dangerous operation', self::$db_error_codes['DB_DELETE_NO_WHERE_CLAUSE']);
        }

        $this->log_error($sql, 'I');

        return $sql;
    }

    /*--------------------------------------------------------------*/

    /**
     * Get the query to fetch only one record of an model.
     *
     * @param flcBaseModel        $p_model the model giving the key fields or at least the id field
     * to get the unique record. At least one of this fields need to exist in the model..
     * @param string|null         $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints the constraints to read only useful in joins.
     *
     * @return string with the select query
     * @throws Exception
     */
    protected function get_read_query(flcBaseModel $p_model, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): string {


        // setup read fields
        //
        $fields = $p_model->get_fields();
        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to read', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // search on field operations to remove not supported in the operation
        foreach ($p_model->get_fields_operations() as $ofield => $soperation) {
            // if it is a computed field? then skip
            if (strpos($soperation, 'c') !== false) {
                continue;
            }

            // if a field with operations defined exist in the field list , but the operation
            // is not suppported remove element.
            if (array_key_exists($ofield, $fields)) {
                if (strpos($soperation, 'r') === false) {
                    unset($fields[$ofield]);
                }
            }
        }


        // verify key fields are defined or at least an id
        $no_all_keys = false;
        $keys = $p_model->get_key_fields();
        if (!isset($keys) || count($keys) == 0) {
            $no_all_keys = true;
        } else {
            foreach ($keys as $id) {
                if (!array_key_exists($id, $fields) || empty($fields[$id])) {
                    $no_all_keys = true;
                }
            }
        }

        if ($no_all_keys) {
            $id_field = $p_model->get_id_field();
            if (empty($id_field)) {
                throw new InvalidArgumentException('Not key fiels or ids are defined for read a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

            }

        }

        $sql = $this->select_clause($fields, $p_model->get_table_name(), $p_constraints);
        $sql .= $this->join_clause($p_constraints);
        $sql .= ' where ';

        // if no key fields use the id.
        if (count($keys) > 0) {
            foreach ($keys as $id) {

                $type = $p_model->get_field_type($id) ?? '';
                $sql .= $id.'='.$this->get_normalized_field_value($fields[$id], $type).' and ';
            }
            $sql = substr($sql, 0, strrpos($sql, ' and '));

        } else {
            $type = $p_model->get_field_type($p_model->get_id_field()) ?? '';
            $sql .= $p_model->get_id_field().'='.$this->get_normalized_field_value($fields[$p_model->get_id_field()], $type);

        }

        $this->log_error($sql, 'I');

        return $sql;
    }




    /*--------------------------------------------------------------*/

    /**
     * Get the fetch query no joins and not referenced entities allowed , in other words only work with
     * the main model fields.
     *
     * @param flcBaseModel        $p_model the main model source of the fields and input values to the constraints.
     * @param flcConstraints|null $p_constraints the constraints.
     * @param string|null         $p_suboperation optional user defined suboperation.
     *
     * @return string with the fetch query.
     * @throws Exception
     */
    protected function get_fetch_query(flcBaseModel $p_model, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null): string {

        // setup fetch fields
        //
        $fields = $p_model->get_fields();
        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to fetch', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // search on field operations to remove not supported in the operation
        foreach ($p_model->get_fields_operations() as $ofield => $soperation) {

            // if a field with operations defined exist in the field list , but the operation
            // is not suppported remove element.
            if (array_key_exists($ofield, $fields)) {
                if (strpos($soperation, 'f') === false) {
                    unset($fields[$ofield]);
                }
            }
        }

        $fields_types = $p_model->get_field_types();


        // referenced entities exist , add this fields to the list of fields from the mein model.
        // this fields added will have prefixd table name.
        // Also add the field types to the fied types from the main model.
        if ($p_constraints !== null) {
            $ref_models = [];

            $joins = $p_constraints->get_joins();

            if ($joins != null) {
                $joins = $joins->get_joins();
                foreach ($joins as $join) {
                    $ref_models[] = $join->get_left_model();
                    $ref_models[] = $join->get_right_model();
                }
            }


            foreach ($ref_models as $p_ref_entity) {
                $table = $p_ref_entity->get_table_name();
                // if the referenced model equals the main model not process (duplicate fields)
                if ($table !== $p_model->get_table_name()) {

                    // process field name => value
                    $f = $p_ref_entity->get_fields();
                    foreach ($f as $field => $value) {
                        $fields[$table.'.'.$field] = $value;
                    }

                    // Process the field types from referenced entities.
                    $ft = $p_ref_entity->get_field_types();
                    foreach ($ft as $field => $value) {
                        $fields_types[$table.'.'.$field] = $value;
                    }
                }

            }
        }

        // create each part of the query.
        $sql = $this->select_clause($fields, $p_model->get_table_name(), $p_constraints);
        $sql .= $this->join_clause($p_constraints);

        //$all_fields = $p_model->get_all_fields('c'); mmm . this was an error!!!!
        // verifying with tests.
        $all_fields = $fields;

        $sql .= $this->where_clause($p_model->get_table_name(), $all_fields, $fields_types, $p_constraints);
        $sql .= $this->order_by_clause($p_model->get_table_name(),$all_fields, $p_model->get_key_fields(), $p_model->get_id_field(), $p_constraints);
        // limit offset (warning , is better with an order by
        if (!empty($p_constraints)) {
            $sql .= ' '.$this->db->get_limit_offset_str($p_constraints->get_start_row(), $p_constraints->get_end_row());

        }

        $this->log_error($sql, 'I');

        return $sql;
    }

    /*--------------------------------------------------------------*/

    /******************************************************************************
     * Utilities for process fetch clauses in parts, useful if we overload
     * get_fetch query for create our own fetch stuff
     */

    /**
     * generate the select clause including join fields if join ecist in constraints.
     * select field1,field2,..... from table_name
     *
     * @param array               $p_fields the list of model fields with values (field=>value each element)
     * @param string              $p_table_name the table name
     * @param flcConstraints|null $p_constraints with the fieds to select , its not
     * defined the whole list of model fields will be used.
     *
     * @return string
     */
    protected function select_clause(array $p_fields, string $p_table_name, ?flcConstraints $p_constraints = null): string {
        // create the select part
        $sql = 'select ';

        // if constraints have a list of fields to use in select use them otherwise
        /// all the model fields
        if (isset($p_constraints)) {
            $sfields = $p_constraints->get_select_fields();
            if ($sfields and count($sfields) > 0) {
                foreach ($sfields as $field) {
                    $sql .= $field.',';
                }

            }
            // use the join fields
            $joins = $p_constraints->get_joins();
            if (isset($joins)) {
                $sql .= $joins->get_join_fields_string().',';
            }
        }

        //if no select fields in constraints and not  join fields
        // use the list in p_fields
        if ($sql == 'select ') {

            foreach ($p_fields as $field => $value) {
                $sql .= $field.',';
            }
        }


        // remove the las charcter (in this case the comma
        $sql = rtrim($sql, ',');
        $sql .= ' from '.$p_table_name;

        return $sql;
    }

    /*--------------------------------------------------------------*/

    /**
     * Generate the join clauses of the query if exist defined in constraints,
     * otherwise join clause will not be generated.
     *
     * @param flcConstraints|null $p_constraints containing the joins clause
     *
     * @return string with the join clause(s) if is requiered.
     */
    protected function join_clause(?flcConstraints $p_constraints): string {
        $sql = '';

        if (isset($p_constraints)) {
            $joins = $p_constraints->get_joins();
            if (isset($joins) && $joins->num_joins() > 0) {
                $sql .= $joins->get_join_string();
            }

        }

        return $sql;
    }

    /*--------------------------------------------------------------*/

    /**
     * Generate the where clause of a query, if where fields are defined on the constraints.
     *
     * In case this where clause is part of a join and we need to add a field from a joined table , this fields
     * in $p_fields need to contain in his name the table name and the field name. for example :
     *
     * t.invoice_header.invoice_number.
     *
     * @param string              $p_table the table name of the main entity
     * @param array               $p_fields the list of model or entities  fields with values (field=>value each
     *     element)
     * @param array               $p_field_types the list of field types (field => type each entry)
     * @param flcConstraints|null $p_constraints the constraints source for the where clause
     *
     * @return string the where clause processed.
     *
     * @see flcBaseModel::get_field_types()
     */
    protected function where_clause(string $p_table, array $p_fields, array $p_field_types, ?flcConstraints $p_constraints = null): string {

        // WHERE
        // search on constraints if are defined
        $sql = '';
        $value = null;
        if (isset($p_constraints)) {
            $where_fields = $p_constraints->get_where_fields();
            if (count($where_fields) > 0) {
                $sql = ' where ';
                foreach ($where_fields as $field) {
                    $operator = '=';
                    // if contain operator normalize
                    if (is_array($field)) {
                        // we expect here field name and operator
                        // te value will be taked from fields array.
                        if (count($field) == 2) {
                            $operator = strtolower($field[1]);
                            $field = $field[0];
                            $value = $p_fields[$field];
                        } else {
                            // We take her only the first three , ignore the unnecessary data
                            // expected is field name,operator,value
                            if (count($field) >= 3) {
                                $operator = strtolower($field[1]);
                                $value = $field[2];
                                $field = $field[0];
                            }
                        }
                    } else {
                        if (isset($p_fields[$field])) {
                            $value = $p_fields[$field];

                        } else {
                            throw new InvalidArgumentException("Where field '$field' doesnt exist in the model", self::$db_error_codes['DB_INVALID_WHERE_FIELD']);
                        }
                    }

                    // if the field is not of a joined table (not have a point)
                    // then prepend the table name  to ensure not ambiguos field
                    $p_origfield = $field;
                    if (strpos($field, '.') === false) {
                        $field = "$p_table.$field";

                    }


                    // if where exist process the other operators
                    $ilike = false;

                    if (strpos($operator, 'like') !== false) {
                        switch ($operator) {
                            case 'nlike';
                            case 'nlike(%-)';
                                $sql .= "$field not like '%";
                                break;

                            case 'like';
                            case 'like(%-)';
                                $sql .= "$field like '%";
                                break;

                            case 'like(-%)':
                                $sql .= "$field like '";
                                break;

                            case 'nilike':
                            case 'nilike(%-)':
                                if (!$this->db->is_ilike_supported()) {
                                    $ilike = true;
                                    $sql .= "lower($field) not like '%";
                                } else {
                                    $sql .= "$field not ilike '%";
                                }
                                break;

                            case 'ilike':
                            case 'ilike(%-)':
                                if (!$this->db->is_ilike_supported()) {
                                    $ilike = true;
                                    $sql .= "lower($field) like '%";
                                } else {
                                    $sql .= "$field ilike '%";
                                }
                                break;

                            case 'nlike(-%)':
                                $sql .= "$field not like '";
                                break;

                            case 'nilike(-%)':
                                if (!$this->db->is_ilike_supported()) {
                                    $ilike = true;
                                    $sql .= "lower($field) not like '";
                                } else {
                                    $sql .= "$field not ilike '";
                                }
                                break;

                            case 'ilike(-%)':
                                if (!$this->db->is_ilike_supported()) {
                                    $ilike = true;
                                    $sql .= "lower($field) like '";
                                } else {
                                    $sql .= "$field ilike '";
                                }
                                break;
                            default :
                                $sql .= " like '";
                        }

                        if ($ilike) {
                            $sql .= strtolower($value);
                        } else {
                            $sql .= $value;
                        }
                    } else {
                        $type = $p_field_types[$p_origfield] ?? '';
                        $sql .= $field.$operator.$this->get_normalized_field_value($value, $type);
                    }


                    if (strpos($operator, 'like') !== false) {
                        switch ($operator) {
                            case 'like';
                            case 'like(-%)';
                            case 'ilike':
                            case 'ilike(-%)':
                            case 'nlike';
                            case 'nlike(-%)';
                            case 'nilike':
                            case 'nilike(-%)':
                                $sql .= "%'";
                                break;
                            default:
                                $sql .= "'";
                        }
                    }
                    $sql .= ' and ';


                }
                $sql = substr($sql, 0, strrpos($sql, ' and '));
            }
        }

        return $sql;

    }

    /*--------------------------------------------------------------*/

    /**
     * Construct the sql string for the order by clause , normally to use a query for
     * fetch records.
     *
     * If not constraints , try to generate order by based on the key fields , if not are specified
     * try to use the unique id of the record , if both exist only key fields will be taken.
     *
     * @param string              $p_table the table name of the main entity*
     * @param array               $p_fields the list of model fields with values (field=>value each element)
     * @param array               $p_key_fields array of names of the fields that compose the unique record.
     * @param mixed               $p_id if not using key fields this field name identify the  unique id field of the
     *     model
     * @param flcConstraints|null $p_constraints the constraints.
     *
     * @return string
     *
     * @see flcConstraints
     *
     */
    protected function order_by_clause(string $p_table, array $p_fields, array $p_key_fields, $p_id, ?flcConstraints $p_constraints = null): string {
        $sql = '';
        if (isset($p_constraints)) {
            // order by
            // if constraints and order by fields are specified then use otherwise
            // use the key fields if exist ,  otherwise id field
            $order_by_fields = $p_constraints->get_order_by_fields();
            if (count($order_by_fields) > 0) {
                $sql .= ' order by ';
                foreach ($order_by_fields as $field => $stype) {

                    // if $field is string is an associative array
                    // otherwise standard array.
                    if (is_string($field)) {
                        $direction = " $stype ";
                    } else {
                        $field = $stype;
                        $direction = '';
                    }


                    if (array_key_exists($field, $p_fields)) {
                        // if the field is not of a joined table (not have a point)
                        // then prepend the table name  to ensure not ambiguos field
                        if (strpos($field, '.') === false) {
                            $field = "$p_table.$field";
                        }

                        $sql .= $field.$direction.',';
                    } else {
                        throw new InvalidArgumentException("Order by field '$field' doesnt exist in the model", self::$db_error_codes['DB_INVALID_WHERE_FIELD']);
                    }

                }
                $sql = rtrim($sql,',');

            }
        }

        // if not already an order by clause , try with key fields otherwise id field
        if (strpos($sql, " order by") === false) {
            $keys = $p_key_fields;
            if (count($keys) > 0) {
                // order by sentence
                $sql .= " order by ";
                foreach ($keys as $key) {
                    $sql .= "$key,";
                }
                $sql = substr($sql, 0, strrpos($sql, ','));

            } else {
                // exist and if field
                /* if (!empty($p_id) && isset($p_fields[$p_id])) {
                     $sql .= " where $p_id = $p_fields[$p_id]";
                 }*/
                $sql .= " order by ".$p_id;
            }
        }


        return $sql;
    }

    /******************************************************************************
     * Utilities
     */


    /**
     * Check if the error array really is a db error.
     *
     * @param array $error the error array to check.
     *
     * @return bool if it is.
     */
    protected function is_db_error(array $error): bool {
        // only message , postgres for example no return error code only message
        if (!empty($error['message'])) {
            return true;
        }

        return false;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return a normalized value based on the type expected.
     *
     * @param mixed  $p_value the original value to process
     * @param string $p_type the output type expected :
     * - 'nostring' means any not string value
     * - 'bool' a boolean value
     * - 'rowversion' for a field that work as the rowversion field.
     *
     * @return bool|int|float|string with the normalized value
     */
    protected function get_normalized_field_value($p_value, string $p_type) {

        $value = $p_value ?? 'NULL';

        if ($p_type == 'rowversion') {
            return $this->db->cast_to_rowversion($value);
        }

        if ($p_type == 'bool') {
            return (in_array($value,['1',1,true,'true'],true) ? "'1'" : "'0'");
        } else {
            if ($p_type == 'nostring' || !is_string($p_value)) {
                return $value;
            } else {
                return "'$value'";
            }
        }
    }

    /*--------------------------------------------------------------*/

    /**
     *
     *  support methods
     */

    /**
     * Simple method to process in case of an exception during any db operatiom.
     *
     * @param Throwable $p_ex
     * @param string    $p_operation what operation is the source of error , can be 'add','update',
     * 'delete','read','fetch'.
     *
     * @return int the db error code.
     * @see self::$db_error_codes
     */
    private function _process_exception(Throwable $p_ex, string $p_operation): int {
        // si es exception posiblemente no es error de database
        $error = $this->db->error();
        if (!$this->is_db_error($error)) {
            $error['code'] = $p_ex->getCode();
            $error['message'] = $p_ex->getMessage();
        } else {
            return $this->_process_db_error($p_operation);
        }

        $this->db->log_error("Error executing $p_operation : {$error['code']} - {$error['message']}", 'E');

        $ret = self::$db_error_codes['DB_OPERATION_FAIL'];
        if ($this->db->is_duplicate_key_error($error)) {
            $ret = self::$db_error_codes['DB_DUPLICATE_KEY'];

        } elseif ($this->db->is_foreign_key_error($error)) {
            $ret = self::$db_error_codes['DB_FOREIGN_KEY_ERROR'];
        }

        return $ret;
    }

    /*--------------------------------------------------------------*/

    /**
     * Simple method to process in case of db error during any db operation.
     *
     * @param string $p_operation what operation is the source of error , can be 'add','update',
     * 'delete','read','fetch'.
     *
     * @return int the db error code.
     * @see self::$db_error_codes
     */
    private function _process_db_error(string $p_operation): int {
        $error = $this->db->error();
        $this->db->log_error("Error executing $p_operation : {$error['code']} - {$error['message']}", 'E');
        $ret = self::$db_error_codes['DB_OPERATION_FAIL'];
        if ($this->db->is_duplicate_key_error($error)) {
            $ret = self::$db_error_codes['DB_DUPLICATE_KEY'];

        } elseif ($this->db->is_foreign_key_error($error)) {
            $ret = self::$db_error_codes['DB_FOREIGN_KEY_ERROR'];

        }

        return $ret;
    }

    /*****************************************************************
     * Log helper
     */

    /**
     * Display an error message
     *
     * @param string $p_errormsg the error message
     * @param string $p_type W-'warning' or E-'error' or 'e' soft error or 'I' - info
     *
     * @throws Exception
     */
    protected function log_error(string $p_errormsg, string $p_type = 'W') {
        try {

            if (flcCommon::is_cli()) {
                echo $p_errormsg.PHP_EOL;
            } else {
                flcCommon::log_message(($p_type == 'E' || $p_type == 'e') ? 'error' : 'info', $p_errormsg);
            }

        } catch (Exception $ex) {
            $msg = 'Imposible to log a message , check your log config ,'.$ex->getMessage();
            // is expected is handled befrore send results to the http server , for cli will be appear in screen.
            throw new RuntimeException($msg);
        }

    }


}