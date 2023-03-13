<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 *
 */

namespace flc\core\model;

use flc\core\accessor\constraints\flcConstraints;
use flc\core\accessor\flcPersistenceAccessor;
use flc\core\accessor\flcPersistenceAccessorAnswer;
use flc\core\dto\flcInputDataProcessor;
use flc\database\driver\flcDriver;
use InvalidArgumentException;

/**
 * Class to represent a model in the persistence , in database that means
 * a record of a table , or a group of joined fields from multiple tables,
 */
class flcBaseModel {

    /**
     * The table name.
     *
     * @var string
     */
    protected string $table_name = '';

    /**
     * List of the key fields that make this model unique.
     *
     * @var array of strings
     */
    protected array $key_fields = [];

    /**
     * If not using key fields this is the name of the unique id of the model.
     *
     * @var string
     */
    protected string $id_field = '';


    /**
     * List of field names => initial values of the model.
     *
     * @var array
     */
    protected array $fields = [];

    /**
     * List of field names => initial values of the model, used only for read
     * operations under joined conditions.
     *
     * @var array
     */
    protected array $fields_ro = [];

    /**
     * List of fields that will be a virtual field because is computed
     * an not a real field, because can be part of a select clause on fetch by example
     * can be treated as part of the model. (virtul of course).
     *
     * @var array
     */
    protected array $fields_computed = [];

    /**
     * List of fields thar support only some operations, the array form
     * is :
     *      field_mame=>'str with operations supported'
     *
     * The supported operations supported are :
     * - c : computed , will be used only on read or fetch operations
     * - r : field will be used in read operations
     * - f : field will be used in fetch operations
     * - a : field will be used in add operations
     * - u : field will be used in update operations.
     *
     * IF a field is supported in all normal operations , dont add to this list.
     *
     * @var array
     */
    protected array $fields_operations = [];


    /**
     * Reference to the accesor that will resolve the persistence operations.
     *
     * @var flcPersistenceAccessor
     */
    protected flcPersistenceAccessor $accessor;

    /**
     * List of field names associated with his types.
     * Now are supported 'rowversion','nostring' or 'bool' otherwise are assumed numerics.
     *
     * @var array
     */
    protected array $field_types = [];

    /**
     * This field is obtained from the field_types array , if that array contains
     * an entry with the type 'rowversion'  that field will be recognized as the
     * rowversion field.
     * NOTES:
     *
     *  postgres had xmin allways
     *
     *  mssql server uses rowversion field but is optional and defined by the user in the record
     *
     *  mysql For MySQL, there is no native 'rowversion' field type, so a 'timestamp' field is used instead. Starting
     *  from version 5.6.4, the resolution of the 'timestamp' field is one microsecond. However, before that version,
     *  the resolution was only one second, which is not ideal for implementing MVCC. It is recommended to avoid using
     *  older versions for better control.
     *  For use the microsecond version the field need to be defined as :
     *          my_rowversion_field TIMESTAMP(6) DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),     *
     *
     *  The "rowversion" field behaves differently depending on the database. For example, in PostgreSQL, within the
     *  same transaction, this field does not change. This means that after adding a record and immediately executing an
     *  update, the value of this field will remain the same. However, unlike PostgreSQL, in MSSQL Server, this field
     *  changes within the same transaction. It is important to know this for the MVCC strategy of the library.
     *
     * @var string with the field that identifies the rowversion field
     *
     */
    protected string $rowversion_field = '';

    protected ?flcInputDataProcessor $input_data = null;

    /**
     * Constructor , need to be called after define the model fields
     * ex:
     *          public function __construct(flcDriver $p_driver, ?flcInputDataProcessor $p_input_data) {
     *              $this->fields = ['numero' => null, 'descripcion' => null, 'customer_id' => null];
     *              $this->fields_ro = ['name' => null];
     *
     *              $this->key_fields = ['numero'];
     *              $this->table_name = 'tb_factura_header';
     *              $this->field_types = ['numero' => 'nostring'];
     *
     *              $this->accessor = new flcDbAccessor($p_driver);
     *
     *              parent::__construct($p_driver, $p_input_data); // --> Important is not a pure model
     *              }
     *
     * @param flcDriver|null             $p_driver is null means not persistence operations allowed (fetch,add,etc cant
     *     be called) in other words is a pure model. Basically will be used for create an instance od the data
     *     accessor if required.
     * @param flcInputDataProcessor|null $p_input_data In case this parameter is defined, it should be an instance of a
     *     flcInputDataProcessor class which will convert input data to model data.
     */
    public function __construct(?flcDriver $p_driver, ?flcInputDataProcessor $p_input_data) {

        // add support to row version field if its allowed
        $rowversion_field = $this->get_rowversion_field();
        // if exist put the operations supported
        if (!empty($rowversion_field)) {
            $this->fields_operations[$rowversion_field] = 'fr';
        }

        if ($p_input_data !== null) {
            $this->input_data = $p_input_data;
            $this->input_data->process_input_data($this);

            // if input data exist get the model fields
            // get the model field values
            foreach ($p_input_data->get_fields() as $field => $value) {
                $this->{$field} = $value;
            }

        }

    }

    /**
     * Return the table name
     *
     * @return string
     */
    public function get_table_name(): string {
        return $this->table_name;
    }

    /**
     * @return array of fields
     */
    public function get_fields(): array {
        return $this->fields;
    }

    /**
     * @return array of key fields
     */
    public function get_key_fields(): array {
        return $this->key_fields ?? [];
    }

    /**
     * Return the array of fields used for read under joined conditions.
     * @return array of fields
     */
    public function get_fields_ro(): array {
        return $this->fields_ro ?? [];
    }

    /**
     * Return the array of computed fields defined.
     * Only valid for read and fetch operations otherwise not valid value
     * will be obtained.
     *
     * @return array of fields=>value
     */
    public function get_fields_computed(): array {
        $fields_computed = [];
        foreach ($this->fields_operations as $field => $operator) {
            if (strpos($operator, 'c') !== false) {
                $fields_computed[$field] = $this->fields_computed[$field];
            }
        }

        return $fields_computed;
    }

    /**
     * Return the array of  fields operations supported
     * @return array of fields
     */
    public function get_fields_operations(): array {
        return $this->fields_operations;
    }


    /**
     * Return the union of all types of fields defined by his type:
     *
     * @param string $p_types an array with a 'c' - for computed fields and/or 'r' for read only fields.
     *
     * @return array
     */
    public function get_all_fields(string $p_types = 'r'): array {
        $all_fields = [];
        if (strpos($p_types, 'r') !== false) {
            $all_fields = array_merge($this->fields, $this->fields_ro);
        }

        if (strpos($p_types, 'c') !== false) {
            $all_fields = array_merge(count($all_fields) > 0 ? $all_fields : $this->fields, $this->get_fields_computed());
        }

        return $all_fields;
    }

    /**
     * @return string the name  of the unique id.
     */
    public function get_id_field(): string {
        return $this->id_field ?? '';
    }


    /**
     * Return the specified field type for a field.
     *
     * @param string $p_fieldname
     *
     * @return string|null the type or null if not defined.
     */
    public function get_field_type(string $p_fieldname): ?string {
        return $this->field_types[$p_fieldname] ?? null;
    }

    /**
     * @return array of the field types assigned.
     */
    public function get_field_types(): array {
        return $this->field_types;
    }

    /**
     * Returns the field that acts as rowversion, determined by the 'field_types' array. If a field indicated as a
     * 'rowversion' type exists, it will be selected, otherwise an empty field will be returned
     *
     * @return string empty if no rowversion field exist.
     */
    public function get_rowversion_field(): string {
        if (empty($this->rowversion_field)) {
            if (in_array('rowversion', $this->field_types)) {
                $this->rowversion_field = array_search('rowversion', $this->field_types);
            }
        }

        return $this->rowversion_field;
    }

    /**
     *
     * Return if a rowversion field is defined , then rowversion is supported
     *
     * @return bool
     */
    public function is_rowversion_supported(): bool {
        if (!empty($this->get_rowversion_field())) {
            return true;
        }

        return false;
    }

    /**
     * Overridable function for validate fields.
     *
     * @param string $p_fieldname
     * @param mixed  $p_value
     *
     * @return bool true if it is ok.
     */
    public function is_valid_field(string $p_fieldname, $p_value): bool {
        return true;
    }

    /*public function is_id_sequence_or_identity() : bool {
        return true;
    }*/

    /**
     * Set the values of the fields of the model.
     *
     * @param array $p_values in the form [name => value] if doesnt exist
     * an exception is throw.
     *
     * @return void
     */
    public function set_values(array $p_values) {
        foreach ($p_values as $field => $value) {
            $this->{$field} = $value;
        }
    }

    /**
     * Return a copy of the model.
     *
     * @return flcBaseModel
     */
    public function &get_copy(): flcBaseModel {
        $class = get_class($this);
        $model = new $class();
        $model->set_values($this->get_fields());

        return $model;
    }


    /**
     * to facilitate access to protected fields of the model
     *
     * @param string $name fieldname
     *
     * @return mixed|null
     */
    public function __get(string $name) {
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            // if array key exist , this means the value is null , return null as value
            if (array_key_exists($name, $this->fields)) {
                return null;
            }

            // now search in fields for read operations
            if (isset($this->fields_ro)) {
                return $this->fields_ro[$name];
            } else {
                // search in computed fields
                if (isset($this->fields_computed)) {
                    return $this->fields_computed[$name];
                } else {
                    // if array key exist , this means the value is null , return null as value
                    if (array_key_exists($name, $this->fields_ro)) {
                        return null;
                    }

                }
            }
            throw new InvalidArgumentException("Field '$name' is not part of the model");
        }
    }

    /**
     * to facilitate access to protected fields of the model
     *
     * @param string $name the fieldname
     * @param mixed  $value the field value
     *
     * @return void
     */
    public function __set($name, $value) {
        if (array_key_exists($name, $this->fields)) {
            if (!$this->is_valid_field($name, $value)) {
                throw new InvalidArgumentException("Invalid value $value for field $name");
            }
            $this->fields[$name] = $value;
        } else {
            // now search in fields for read operations
            if (array_key_exists($name, $this->fields_ro)) {
                if (!$this->is_valid_field($name, $value)) {
                    throw new InvalidArgumentException("Invalid value $value for field(ro) $name");
                }
                $this->fields_ro[$name] = $value;
            } else {
                // now search in computed fields
                if (array_key_exists($name, $this->fields_computed)) {
                    if (!$this->is_valid_field($name, $value)) {
                        throw new InvalidArgumentException("Invalid value $value for field(computed) $name");
                    }
                    $this->fields_computed[$name] = $value;
                } else {
                    throw new InvalidArgumentException("Field '$name' is not part of the model");
                }
            }
        }
    }

    /**
     * Persistence stuff
     */

    /**
     * Do the add operation to the persistence
     *
     * @param string|null $p_suboperation
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     *
     */
    public function add(?string $p_suboperation = null): flcPersistenceAccessorAnswer {
        $c = $this->get_add_constraints($p_suboperation);

        return $this->accessor->add($this, $p_suboperation, $c);

    }

    /**
     * Do the read operation on the persistence
     *
     * @param string|null $p_suboperation
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     *
     */
    public function read(?string $p_suboperation = null): flcPersistenceAccessorAnswer {
        $c = $this->get_read_constraints($p_suboperation);

        return $this->accessor->read($this, $p_suboperation, $c);
    }

    /**
     * Do the update operation to the persistence
     *
     * @param string|null $p_suboperation
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     *
     */
    public function update(?string $p_suboperation = null): flcPersistenceAccessorAnswer {
        // update to  the model doesnt require specific constraints , but because need to
        // read the records after an update for update the model , we need to pass
        // the read constraints.
        $c = $this->get_read_constraints($p_suboperation);

        return $this->accessor->update($this, $p_suboperation, $c);

    }

    /**
     * Do the delete operation to the persistence
     *
     * @param string|null $p_suboperation
     * @param bool        $p_verify_delete_check is true , check is the model to delete is already deleted
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     *
     */
    public function delete(?string $p_suboperation = null, bool $p_verify_delete_check = true): flcPersistenceAccessorAnswer {
        $c = $this->get_delete_constraints($p_suboperation);
        if ($c) {
            return $this->accessor->delete_full($this, $c);

        } else {
            return $this->accessor->delete($this, $p_verify_delete_check);

        }

    }

    /**
     * Do the fetch operation to the persistence
     *
     * @param string|null $p_suboperation
     *
     * @return flcPersistenceAccessorAnswer with the answer.
     * @see flcPersistenceAccessorAnswer
     *
     */
    public function fetch(?string $p_suboperation = null): flcPersistenceAccessorAnswer {

        $c = $this->get_fetch_constraints($p_suboperation);

        return $this->accessor->fetch($this, $c, $p_suboperation);

    }

    /***************************************************************
     * Constraints stuff
     */

    /**
     * Get the fetch constraints based on sub operation.
     *
     * @param string|null $p_suboperation
     *
     * @return flcConstraints|null
     */
    public function &get_fetch_constraints(?string $p_suboperation = null): ?flcConstraints {
        // from the input data can be an specified constraints to use as default.
        if ($this->input_data) {
            $x = $this->input_data->get_constraints('fetch');
        } else {
            $x = null;
        }

        return $x;
    }

    /**
     * Get the delete constraints based on sub operation.
     *
     * @param string|null $p_suboperation
     *
     * @return flcConstraints|null
     */
    public function &get_delete_constraints(?string $p_suboperation = null): ?flcConstraints {
        $x = null;

        return $x;
    }

    /**
     * Get the read constraints based on sub operation.
     *
     * @param string|null $p_suboperation
     *
     * @return flcConstraints|null
     */
    public function &get_read_constraints(?string $p_suboperation = null): ?flcConstraints {
        $x = null;

        return $x;
    }

    /**
     * Get the add constraints based on sub operation.
     *
     * @param string|null $p_suboperation
     *
     * @return flcConstraints|null
     */
    public function &get_add_constraints(?string $p_suboperation = null): ?flcConstraints {
        $x = null;

        return $x;
    }


}