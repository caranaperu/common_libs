<?php
/**
 * This file is part of Future Labs Code 1 framework.
 **
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 */

namespace framework\core\accessor;


use framework\core\accessor\constraints\flcConstraints;
use framework\core\entity\flcBaseEntity;
use framework\database\driver\flcDriver;
use InvalidArgumentException;
use Throwable;

/**
 * Specific implementation of a persistence accessor for daabases-
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

    /**
     * @inheritdoc
     */
    public function add(flcBaseEntity &$p_entity, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): int {

        $res = null;

        try {

            // do add
            $res = $this->db->execute_query($this->get_add_query($p_entity, $p_suboperation));
            if ($res) {
                echo 'affected rows add '.$res->affected_rows().PHP_EOL;

                $id_field = $p_entity->get_id_field();

                // try to get the las insert id , otherwise will read from the keys
                if (!empty($id_field)) {
                    $p_id_value = $this->db->insert_id($p_entity->get_table_name(), $id_field);
                    if ($p_id_value > 0) {
                        $p_entity->set_values([$id_field => $p_id_value]);
                    }
                }

                // Read to load all the values from the record in the entity , important to update some automatic fields
                // or fields with defaults not currently in the received entity..
                $ret = $this->read($p_entity, $p_suboperation, $p_constraints);

            } else {
                $ret = $this->_process_db_error('add');
            }

        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'add');
        } finally {

            if ($res) {
                $res->free_result();
            }

        }


        // announce we have an error
        if ($ret !== self::$db_error_codes['DB_OPERATION_OK']) {
            $this->db->trans_mark_dirty();
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function update(flcBaseEntity &$p_entity, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): int {

        $res = null;

        try {

            $rowversion_support = $this->is_rowversion_supported($p_entity);

            $sql = $this->get_update_query($p_entity, $p_suboperation);

            $res = $this->db->execute_query($sql);

            if ($res) {
                echo 'affected rows update '.$res->affected_rows().PHP_EOL;
                $affected_rows = $res->affected_rows();


                if ($affected_rows <= 0) {
                    // Probably nothing in the record is change or a real error ocurred
                    // check.
                    $error = $this->db->error();

                    if (!$this->is_db_error($error)) {
                        // if simply nothing was changed , then no error , next step
                        // reread to verify changes.
                        $ret = $this->read($p_entity, $p_suboperation);

                        if ($rowversion_support) {
                            // its deleted or modified?
                            // its delete will return self::$db_error_codes['DB_RECORD_NOT_EXIST']
                            if ($ret == self::$db_error_codes['DB_OPERATION_OK']) {
                                $ret = self::$db_error_codes['DB_RECORD_MODIFIED'];
                            }

                        }
                    } else {
                        $ret = $this->_process_db_error('update');
                    }

                } else {
                    $ret = $this->read($p_entity, $p_suboperation, $p_constraints);
                }


            } else {
                $ret = $this->_process_db_error('update');
            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'update');
        } finally {

            if ($res) {
                $res->free_result();
            }
        }

        // announce we have an error
        if ($ret !== self::$db_error_codes['DB_OPERATION_OK']) {
            $this->db->trans_mark_dirty();
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function delete(flcBaseEntity &$p_entity, bool $p_verify_deleted_check = true): int {

        $ret = self::$db_error_codes['DB_OPERATION_OK'];

        try {
            $res = $this->db->execute_query($this->get_delete_query($p_entity));

            if ($res) {
                echo 'affected rows delete '.$res->affected_rows().PHP_EOL;
                if ($p_verify_deleted_check) {
                    if ($res->affected_rows() <= 0) {
                        // Is modified or not exist.
                        $ret = $this->read($p_entity);
                        // if we can read without taking account of the rowversion field , means is modificed
                        // otherwise doesnt exist.
                        if ($ret == self::$db_error_codes['DB_OPERATION_OK']) {
                            $ret = self::$db_error_codes['DB_RECORD_MODIFIED'];
                        } else {
                            $ret = self::$db_error_codes['DB_RECORD_NOT_EXIST'];
                        }
                    }
                }
            } else {
                $ret = $this->_process_db_error('delete');
            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'delete');
        }

        // announce we have an error
        if ($ret !== self::$db_error_codes['DB_OPERATION_OK']) {
            $this->db->trans_mark_dirty();
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function delete_full(flcBaseEntity &$p_entity, flcConstraints $p_constraints): int {
        $ret = self::$db_error_codes['DB_OPERATION_OK'];

        try {
            $res = $this->db->execute_query($this->get_delete_query_full($p_entity, $p_constraints));

            if ($res) {
                echo 'affected rows delete '.$res->affected_rows().PHP_EOL;
                if ($res->affected_rows() <= 0) {
                    $ret = self::$db_error_codes['DB_RECORD_NOT_EXIST'];
                }
            } else {
                $ret = $this->_process_db_error('delete full');
            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'delete full');
        }

        // announce we have an error
        if ($ret !== self::$db_error_codes['DB_OPERATION_OK']) {
            $this->db->trans_mark_dirty();
        }

        return $ret;
    }

    /**
     * @inheritdoc
     */
    public function read(flcBaseEntity &$p_entity, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): int {

        $res = null;
        $ret = self::$db_error_codes['DB_OPERATION_OK'];
        try {

            $res = $this->db->execute_query($this->get_read_query($p_entity, $p_suboperation, $p_constraints));

            if ($res) {
                if ($res->num_rows() == 0) {
                    $ret = self::$db_error_codes['DB_RECORD_NOT_EXIST'];
                } else {
                    $p_entity->set_values($res->row_array());
                }

            } else {
                $ret = $this->_process_db_error('read');
            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'read');
        } finally {
            if ($res) {
                $res->free_result();
            }

        }
        // announce we have an error
        if ($ret == self::$db_error_codes['DB_OPERATION_FAIL']) {
            $this->db->trans_mark_dirty();
        }

        return $ret;
    }


    /**
     * @inheritdoc
     */
    public function fetch(flcBaseEntity $p_entity, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null) {
        return $this->fetch_full($p_entity, null, $p_constraints, $p_suboperation);
    }

    /**
     * @inheritdoc
     */
    public function fetch_full(flcBaseEntity $p_entity, ?array $p_ref_entities, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null) {
        try {
            $sql = $this->get_fetch_query_full($p_entity, $p_ref_entities, $p_constraints, $p_suboperation);
            $res = $this->db->execute_query($sql);
            if ($res) {
                $ret = $res->result_array();
            } else {
                $ret = $this->_process_db_error('fetch full');
            }
        } catch (Throwable $ex) {
            $ret = $this->_process_exception($ex, 'fetch full');
        }

        // announce we have an error
        if ($ret == self::$db_error_codes['DB_OPERATION_FAIL']) {
            $this->db->trans_mark_dirty();
        }

        return $ret;
    }


    protected function get_add_query(flcBaseEntity $p_entity, ?string $p_suboperation = null): string {
        $fields = $p_entity->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to read', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // verify key fields are defined or at least an id
        $no_all_keys = false;
        $ids = $p_entity->get_key_fields();
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
            $id_field = $p_entity->get_id_field();
            if (empty($id_field)) {
                throw new InvalidArgumentException('Not key fiels or ids are defined for add a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

            }

        }

        $id_field = $p_entity->get_id_field();

        $sql = "INSERT INTO {$p_entity->get_table_name()} (".implode(',', array_keys($fields)).') VALUES (';
        // remove unique id from list if exist
        if (!empty($id_field)) {
            $sql = str_replace(["$id_field,", "$id_field"], '', $sql);

        }

        foreach ($fields as $field => $value) {
            // if unique id , or autoincrement cant add to add query.
            if ($p_entity->get_id_field() !== $field) {
                $type = $p_entity->get_field_type($field) ?? '';

                $sql .= $this->get_normalized_field_value($value, $type).',';

            }
        }

        $sql =  substr($sql,0,strrpos($sql,','));
        $sql .= ')';

        echo $sql.PHP_EOL;

        return $sql;
    }

    protected function get_update_query(flcBaseEntity $p_entity, ?string $p_suboperation = null): string {
        $fields = $p_entity->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to read', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // verify key fields are defined or at least an id
        $no_all_keys = false;
        $keys = $p_entity->get_key_fields();
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
            $id_field = $p_entity->get_id_field();
            if (empty($id_field)) {
                throw new InvalidArgumentException('Not key fiels or ids are defined for read a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

            }

        }

        // update list
        $sql = 'update '.$p_entity->get_table_name().' set ';
        foreach ($fields as $field => $value) {
            $type = $p_entity->get_field_type($field) ?? '';

            $sql .= $field.'='.$this->get_normalized_field_value($value, $type).',';
        }

        // where list
        $sql =  substr($sql,0,strrpos($sql,','));
        $sql .= ' where ';


        // if no key fields use the id.
        if (count($keys) > 0) {
            foreach ($keys as $key) {
                $type = $p_entity->get_field_type($key) ?? '';

                $sql .= $key.'='.$this->get_normalized_field_value($fields[$key], $type).' and ';
            }
        } else {
            $sql .= $p_entity->get_id_field().'='.$fields[$p_entity->get_id_field()].' and ';

        }

        // add rowversion field to where
        // check if row version is supported
        if ($this->is_rowversion_supported($p_entity)) {
            $value = $p_entity->{$this->db->get_rowversion_field()};
            $type = $p_entity->get_field_type($this->db->get_rowversion_field()) ?? '';

            $sql .= $this->db->get_rowversion_field().' = '.$this->get_normalized_field_value($value, $type);
        }
        $sql =  substr($sql,0,strrpos($sql,' and '));

        echo $sql.PHP_EOL;

        return $sql;

    }

    /**
     * Return the query required to delete one instance of the entity and only one based in the key fields if exist or
     * the uniqye id if no keys defined.
     *
     * @param flcBaseEntity $p_entity the entity values requiered for delete.
     *
     * @return string with the delete query
     */
    protected function get_delete_query(flcBaseEntity $p_entity): string {
        $fields = $p_entity->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to delete', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        // verify key fields are defined or at least an id
        $no_all_keys = false;
        $keys = $p_entity->get_key_fields();
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
            $id_field = $p_entity->get_id_field();
            if (empty($id_field)) {
                throw new InvalidArgumentException('Not key fiels or ids are defined for delete a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

            }

        }

        $sql = "delete from {$p_entity->get_table_name()} where ";

        // if no key fields use the id.
        if (count($keys) > 0) {
            foreach ($keys as $key) {
                $type = $p_entity->get_field_type($key) ?? '';
                $sql .= $key.'='.$this->get_normalized_field_value($fields[$key], $type).' and ';
            }
        } else {
            $sql .= $p_entity->get_id_field().'='.$fields[$p_entity->get_id_field()].' and ';

        }

        // add rowversion field to where
        // check if row version is supported
        if ($this->is_rowversion_supported($p_entity)) {
            $value = $p_entity->{$this->db->get_rowversion_field()};
            $type = $p_entity->get_field_type($this->db->get_rowversion_field()) ?? '';

            $sql .= $this->db->get_rowversion_field().' = '.$this->get_normalized_field_value($value, $type);
        }

        $sql =  substr($sql,0,strrpos($sql,' and '));

        echo $sql.PHP_EOL;

        return $sql;
    }


    /**
     * Return the query required to delete one or more instances of the entity based on the constraints.
     *
     * @param flcBaseEntity  $p_entity
     * @param flcConstraints $p_constraints
     *
     * @return string with the delete query
     */
    public function get_delete_query_full(flcBaseEntity $p_entity, flcConstraints $p_constraints): string {
        $fields = $p_entity->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to delete', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }

        $sql = "delete from {$p_entity->get_table_name()} ";
        $sqlwhere = $this->where_clause($fields, $p_entity->get_field_types(), $p_constraints);

        if ($sqlwhere != '') {
            $sql .= $sqlwhere;
        } else {
            // Not delete without where !!!!!!
            throw new InvalidArgumentException('Not where clause defined to delete, dangerous operation', self::$db_error_codes['DB_DELETE_NO_WHERE_CLAUSE']);
        }
        echo $sql.PHP_EOL;

        return $sql;
    }

    /**
     * Get the query to fetch only one record of an entity.
     *
     * @param flcBaseEntity       $p_entity the entity giving the key fields or at least the id field
     * to get the unique record. At least one of this fields need to exist in the entity..
     * @param string|null         $p_suboperation optional user defined suboperation.
     * @param flcConstraints|null $p_constraints the constraints to read only useful in joins.
     *
     * @return string with the select query
     */
    protected function get_read_query(flcBaseEntity $p_entity, ?string $p_suboperation = null, ?flcConstraints $p_constraints = null): string {

        $fields = $p_entity->get_fields();

        if (count($fields) == 0) {
            throw new InvalidArgumentException('Not defined fields to read', self::$db_error_codes['DB_NO_FIELDS_TO_EXECUTE']);
        }


        // verify key fields are defined or at least an id
        $no_all_keys = false;
        $keys = $p_entity->get_key_fields();
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
            $id_field = $p_entity->get_id_field();
            if (empty($id_field)) {
                throw new InvalidArgumentException('Not key fiels or ids are defined for read a record', self::$db_error_codes['DB_NOT_UNIQUE_ID']);

            }

        }

        $sql = $this->select_clause($fields, $p_entity->get_table_name(), $p_constraints);
        $sql .= $this->join_clause($p_constraints);
        $sql .= ' where ';
        // create the
        /*        $sql = 'select ';
                foreach ($fields as $field => $value) {
                    $sql .= $field.',';
                }
                $sql = rtrim($sql, ',');
                $sql .= ' from '.$p_entity->get_table_name().' where ';
        */
        // if no key fields use the id.
        if (count($keys) > 0) {
            foreach ($keys as $id) {

                $type = $p_entity->get_field_type($id) ?? '';
                $sql .= $id.'='.$this->get_normalized_field_value($fields[$id], $type).' and ';
            }
            $sql =  substr($sql,0,strrpos($sql,' and '));

        } else {
            $type = $p_entity->get_field_type($p_entity->get_id_field()) ?? '';
            $sql .= $p_entity->get_id_field().'='.$this->get_normalized_field_value($fields[$p_entity->get_id_field()], $type);

        }


        echo $sql.PHP_EOL;

        return $sql;
    }

    /**
     * Get the fetch query no joins and not referenced entities allowed , in other words only work with
     * the main entity fields.
     *
     * @param flcBaseEntity       $p_entity the main entity source of the fields and input values to the constraints.
     * @param flcConstraints|null $p_constraints the constraints.
     * @param string|null         $p_suboperation optional user defined suboperation.
     *
     * @return string with the fetch query.
     */
    protected function get_fetch_query(flcBaseEntity $p_entity, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null): string {
        return $this->get_fetch_query_full($p_entity, null, $p_constraints, $p_suboperation);
    }

    /**
     * This version to construct the fetch query allow join clauses using different tables and where clauses with
     * fields from referenced entities (tables).
     *
     * @param flcBaseEntity       $p_entity the main entity source of the fields and input values to the constraints.
     * @param array|null          $p_ref_entities an array of entities in case we need to reference other table fields
     *     in constraints.
     * @param flcConstraints|null $p_constraints the constraints.
     * @param string|null         $p_suboperation optional user defined suboperation.
     *
     * @return string with the fetch query.
     */
    protected function get_fetch_query_full(flcBaseEntity $p_entity, ?array $p_ref_entities, ?flcConstraints $p_constraints = null, ?string $p_suboperation = null): string {

        $fields = $p_entity->get_fields();
        $fields_types = $p_entity->get_field_types();

        // referenced entities exist , add this fields to the list of fields from the mein entity.
        // this fields added will have prefixd table name.
        // Also add the field types to the fied types from the main entity.
        if ($p_ref_entities !== null) {
            foreach ($p_ref_entities as $p_ref_entity) {
                $table = $p_ref_entity->get_table_name();

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

        // create each part of the query.
        $sql = $this->select_clause($fields, $p_entity->get_table_name(), $p_constraints);
        $sql .= $this->join_clause($p_constraints);
        $sql .= $this->where_clause($fields, $fields_types, $p_constraints);
        $sql .= $this->order_by_clause($fields, $p_entity->get_key_fields(), $p_entity->get_id_field(), $p_constraints);
        // limit offset (warning , is better with an order by
        if (!empty($p_constraints)) {
            $sql .= ' '.$this->db->get_limit_offset_str($p_constraints->get_start_row(), $p_constraints->get_end_row());

        }

        echo $sql;

        return $sql;
    }

    /******************************************************************************
     * Utilities for process fetch clauses in parts, useful if we overload
     * get_fetch query for create our own fetch stuff
     */

    /**
     * generate the select clause including join fields if join ecist in constraints.
     * select field1,field2,..... from table_name
     *
     * @param array               $p_fields the list of entity fields with values (field=>value each element)
     * @param string              $p_table_name the table name
     * @param flcConstraints|null $p_constraints with the fieds to select , its not
     * defined the whole list of entity fields will be used.
     *
     * @return string
     */
    protected function select_clause(array $p_fields, string $p_table_name, ?flcConstraints $p_constraints = null): string {
        // create the select part
        $sql = 'select ';

        // if constraints have a list of fields to use in select use them otherwise
        /// al the entity fields
        if (isset($p_constraints)) {
            $sfields = $p_constraints->get_select_fields();
            if ($sfields and count($sfields) > 0) {
                foreach ($sfields as $field) {
                    $sql .= $field.',';
                }

                $joins = $p_constraints->get_joins();
                if (isset($joins)) {
                    $sql .= $joins->get_join_fields_string().',';
                }
            }
        }
        // if no select fields in constraints , use the entity fields
        if (!isset($sfields) or count($sfields) == 0) {
            foreach ($p_fields as $field => $value) {
                $sql .= $field.',';
            }
        }


        $sql =  substr($sql,0,strrpos($sql,','));
        $sql .= ' from '.$p_table_name;

        return $sql;
    }

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


    /**
     * Generate the where clause of a query, if where fields are defined on the constraints.
     *
     * In case this where clause is part of a join and we need to add a field from a joined table , this fields
     * in $p_fields need to contain in his name the table name and the field name. for example :
     *
     * t.invoice_header.invoice_number.
     *
     * @param array               $p_fields the list of entity or entities  fields with values (field=>value each
     *     element)
     * @param array               $p_field_types the list of field types (field => type each entry)
     * @param flcConstraints|null $p_constraints the constraints source for the where clause
     *
     * @return string the where clause processed.
     *
     * @see flcBaseEntity::get_field_types()
     */
    protected function where_clause(array $p_fields, array $p_field_types, ?flcConstraints $p_constraints = null): string {
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
                            throw new InvalidArgumentException("Where field '$field' doesnt exist in the entity", self::$db_error_codes['DB_INVALID_WHERE_FIELD']);
                        }
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
                        $type = $p_field_types[$field] ?? '';
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
                $sql =  substr($sql,0,strrpos($sql,' and '));
            }
        }

        return $sql;

    }

    /**
     * Construct the sql string for the order by clause , normally to use a query for
     * fetch records.
     *
     * If not constraints , try to generate order by based on the key fields , if not are specified
     * try to use the unique id of the record , if both exist only key fields will be taken.
     *
     * @param array               $p_fields the list of entity fields with values (field=>value each element)
     * @param array               $p_key_fields array of names of the fields that compose the unique record.
     * @param mixed               $p_id if not using key fields this field name identify the  unique id field of the
     *     entity
     * @param flcConstraints|null $p_constraints the constraints.
     *
     * @return string
     *
     * @see flcConstraints
     *
     */
    protected function order_by_clause(array $p_fields, array $p_key_fields, $p_id, ?flcConstraints $p_constraints = null): string {
        $sql = '';
        if (isset($p_constraints)) {
            // order by
            // if constraints and order by fields are specified then use otherwise
            // use the key fields if exist ,  otherwise id field
            $order_by_fields = $p_constraints->get_order_by_fields();
            if (count($order_by_fields) > 0) {
                $sql .= ' order by ';
                foreach ($order_by_fields as $field) {

                    // if is an array we expect $field=>$type (asc or desc)
                    if (is_array($field)) {
                        $direction = " $field[1] ";
                        $field = $field[0];
                    } else {
                        $direction = '';
                    }

                    if (isset($p_fields[$field])) {
                        $sql .= $field.$direction.',';
                    } else {
                        throw new InvalidArgumentException("Order by field '$field' doesnt exist in the entity", self::$db_error_codes['DB_INVALID_WHERE_FIELD']);
                    }

                }
                $sql =  substr($sql,0,strrpos($sql,','));

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
                $sql =  substr($sql,0,strrpos($sql,','));

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
        if (!empty($error->message)) {
            return true;
        }

        return false;
    }

    /**
     * Search on the array of key fields of the entity if the rowversion field
     * that the database support exist.
     *
     * @param flcBaseEntity $p_entity the entity to check if support rowversion field.
     *
     * @return bool true if rowversion is supported and also is part of the key on the entity.
     */
    protected function is_rowversion_supported(flcBaseEntity $p_entity): bool {
        $rowversion_field = $this->db->get_rowversion_field();
        if (!empty($rowversion_field)) {
            if (array_key_exists($rowversion_field, $p_entity->get_fields())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Return a normalized value based on the type expected.
     *
     * @param mixed  $p_value the original value to process
     * @param string $p_type the output type expected :
     * - 'nostring' means any not string value
     * - 'bool' a boolean value
     * otherwise treat as any type of number
     *
     * @return bool|int|float|string with the normalized value
     */
    protected function get_normalized_field_value($p_value, string $p_type) {

        $value = $p_value ?? 'NULL';

        if ($p_type == 'bool') {
            return ($value ? '1' : '0');
        } else {
            if ($p_type == 'nostring' || !is_string($p_value)) {
                return $value;
            } else {
                return "'$value'";
            }
        }
    }

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
        if ($this->is_db_error($error)) {
            $this->db->log_error("Cant execute $p_operation", 'E');
        } else {
            $this->db->log_error("Cant execute read - {$p_ex->getMessage()} / {$p_ex->getCode()}", 'e');
        }

        return self::$db_error_codes['DB_OPERATION_FAIL'];
    }

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
}