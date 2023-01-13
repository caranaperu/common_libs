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

namespace framework\core\entity;

use framework\core\accessor\constraints\flcConstraints;
use framework\core\accessor\flcPersistenceAccessor;
use framework\core\dto\flcInputData;
use framework\database\driver\flcDriver;
use InvalidArgumentException;

/**
 * Class to represent a model/entity in the persistence , in database that means
 * a record of a table , or a group of joined fields from multiple tables,
 */
class flcBaseEntity {

    /**
     * The table name.
     *
     * @var string
     */
    protected string $table_name = '';

    /**
     * List of the key fields that make this entity unique.
     *
     * @var array of strings
     */
    protected array $key_fields = [];

    /**
     * If not using key fields this is the name of the unique id of the entity.
     *
     * @var string
     */
    protected string $id_field = '';


    /**
     * List of field names => initial values of the entity.
     *
     * @var array
     */
    protected array $fields = [];

    /**
     * List of field names => initial values of the entity, used only for read
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
     * Now are supported 'nostring' or 'bool' otherwise are assumed numerics.
     *
     * @var array
     */
    protected array $field_types = [];

    protected ?flcInputData $input_data = null;

    /**
     * Constructor , need to be called after define the model/entity fields
     * ex:
     *          public function __construct(flcDriver $p_driver, ?flcInputData $p_input_data) {
     *              $this->fields = ['numero' => null, 'descripcion' => null, 'customer_id' => null];
     *              $this->fields_ro = ['name' => null];
     *
     *              $this->key_fields = ['numero'];
     *              $this->table_name = 'tb_factura_header';
     *              $this->field_types = ['numero' => 'nostring'];
     *
     *              $this->accessor = new flcDbAccessor($p_driver);
     *
     *              parent::__construct($p_driver, $p_input_data);
     *              }
     *
     * @param flcDriver         $p_driver
     * @param flcInputData|null $p_input_data
     */
    public function __construct(flcDriver $p_driver, ?flcInputData $p_input_data) {

        if ($p_input_data !== null) {
            $this->input_data = $p_input_data;
            $this->input_data->process_input_data($this);

            // if input data exist get the model/entity fields
            // get the model field values
            foreach ($p_input_data->get_fields() as $field => $value) {
                $this->{$field} = $value;
            }

        }

        // add support to row version field if its allowed
        if ($p_driver->get_rowversion_field() !== null) {
            $this->fields[$p_driver->get_rowversion_field()] = null;
            $this->field_types[$p_driver->get_rowversion_field()] = 'nostring';
            $this->fields_operations[$p_driver->get_rowversion_field()] = 'fr';
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
     * @return array of fields
     */
    public function get_fields_computed(): array {
        $fields_computed = [];
        foreach($this->fields_operations as $field => $operator) {
            if (strpos($operator,'c') !== false) {
                $fields_computed[] = $field;
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
            $all_fields = array_merge($this->fields, $this->get_fields_computed());
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
     * Set the values of the fields of the entity.
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
     * Return a copy of the entity.
     *
     * @return flcBaseEntity
     */
    public function &get_copy(): flcBaseEntity {
        $class = get_class($this);
        $model = new $class();
        $model->set_values($this->get_fields());

        return $model;
    }


    /**
     * to facilitate access to protected fields of the entity
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
     * to facilitate access to protected fields of the entity
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
     * @return int with the error code
     */
    public function add(?string $p_suboperation = null): int {
        $c = $this->get_read_constraints($p_suboperation);

        return $this->accessor->add($this, $p_suboperation, $c);

    }

    /**
     * Do the read operation on the persistence
     *
     * @param string|null $p_suboperation
     *
     * @return int with the error code
     */
    public function read(?string $p_suboperation = null): int {
        $c = $this->get_read_constraints($p_suboperation);

        return $this->accessor->read($this, $p_suboperation, $c);
    }

    /**
     * Do the update operation to the persistence
     *
     * @param string|null $p_suboperation
     *
     * @return int with the error code
     */
    public function update(?string $p_suboperation = null): int {
        $c = $this->get_read_constraints($p_suboperation);

        return $this->accessor->update($this, $p_suboperation, $c);

    }

    /**
     * Do the delete operation to the persistence
     *
     * @param string|null $p_suboperation
     * @param bool        $p_verify_delete_check is true , check is the entity to delete is already deleted
     *
     * @return int with the error code
     */
    public function delete(?string $p_suboperation = null, bool $p_verify_delete_check = true): int {
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
     * @param array|null  $p_ref_entities if its joined fetch , set the referenced entities.
     *
     * @return array|array[]|int with the error code
     */
    public function fetch(?string $p_suboperation = null, ?array $p_ref_entities = null) {

        $c = $this->get_fetch_constraints($p_suboperation);
        if ($p_ref_entities === null) {
            return $this->accessor->fetch($this, $c, $p_suboperation);

        } else {
            return $this->accessor->fetch_full($this, $p_ref_entities, $c, $p_suboperation);

        }
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