<?php

namespace framework\core\model;

use InvalidArgumentException;

class flcBaseModel {

    /**
     * @var mixed
     */
    protected array $ids;

    protected string $table_name = '';

    /**
     * @var array
     */
    protected array $fields = [];

    /**
     * List of field names that will be used for check if a record is changed.
     *
     * @var array
     */
    protected array $field_types = [];

    public function get_table_name(): string {
        return $this->table_name;
    }

    public function get_fields(): array {
        return $this->fields;
    }

    public function get_ids(): array {
        return $this->ids;
    }

    public function get_field_type(string $p_fieldname) : ?string  {
        return $this->field_types[$p_fieldname] ?? null;
    }

    public function __get($name) {
        // TODO: Implement __get() method.
        if (isset($this->fields[$name])) {
            return $this->fields[$name];
        } else {
            throw new InvalidArgumentException("Field $name is not part of the model");
        }
       // return null;
    }

    public function __set($name, $value) {
        // TODO: Implement __get() method.
        if (array_key_exists($name,$this->fields)) {
            $this->fields[$name] = $value;
        } else {
            throw new InvalidArgumentException("Field $name is not part of the model");
        }
    }

    public function set_values(array $p_values) {
        foreach ($p_values as $field=>$value) {
            $this->{$field} = $value;
        }
    }

    public function &getCopy() : flcBaseModel {
        $class = get_class($this);
        $model = new $class();
        $model->set_values($this->get_fields());
        return $model;
    }

}