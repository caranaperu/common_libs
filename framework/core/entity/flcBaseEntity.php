<?php

namespace framework\core\entity;

use framework\core\accessor\constraints\flcConstraints;
use framework\core\accessor\flcPersistenceAccessor;
use framework\core\dto\flcInputData;
use framework\database\driver\flcDriver;
use InvalidArgumentException;

/**
 * Class to represent an entity in the persistence , in database that means
 * a record of a table , nothing more , for things like part of one record entity
 * plus other fields of other entities (joined fetch by example) use models.
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

    protected ?flcInputData $input_data=null;

    public function __construct(flcDriver $p_driver,?flcInputData $p_input_data) {
        $this->input_data = $p_input_data;
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
     * @return string the name  of the unique id.
     */
    public function get_id_field(): string {
        return $this->id_field ?? '';
    }

    public function get_all_fields(): array {
        return array_merge($this->fields, $this->fields_ro);
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
    public function &getCopy(): flcBaseEntity {
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
                // if array key exist , this means the value is null , return null as value
                if (array_key_exists($name, $this->fields_ro)) {
                    return null;
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
                throw new InvalidArgumentException("Field '$name' is not part of the model");
            }
        }
    }

    /**
     * Persistence stuff
     */
    public function add(?string $p_suboperation = null): int {
        $c = $this->get_read_constraints($p_suboperation);

        return $this->accessor->add($this, $p_suboperation, $c);

    }

    public function read(?string $p_suboperation = null): int {
        $c = $this->get_read_constraints($p_suboperation);

        return $this->accessor->read($this, $p_suboperation, $c);
    }

    public function update(?string $p_suboperation = null): int {
        $c = $this->get_read_constraints($p_suboperation);

        return $this->accessor->update($this, $p_suboperation, $c);

    }

    public function delete(?string $p_suboperation = null, bool $p_verify_delete_check = true): int {
        $c = $this->get_delete_constraints($p_suboperation);
        if ($c) {
            return $this->accessor->delete_full($this, $c);

        } else {
            return $this->accessor->delete($this, $p_verify_delete_check);

        }

    }

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
     * @param string|null $p_suboperation
     *
     * @return flcConstraints|null
     */
    public function &get_fetch_constraints(?string $p_suboperation = null): ?flcConstraints {
        $x = null;

        return $x;
    }

    public function &get_delete_constraints(?string $p_suboperation = null): ?flcConstraints {
        $x = null;

        return $x;
    }

    public function &get_read_constraints(?string $p_suboperation = null): ?flcConstraints {
        $x = null;

        return $x;
    }

}