<?php
/**
 * This file is part of Future Labs Code 1 framework.
 **
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 */
namespace framework\core\accessor\constraints;

use InvalidArgumentException;

/**
 * Class that defines the constraints to be used in persistence accesors
 */
class flcConstraints {
    protected int $start_row = 0;
    protected int $end_row   = 0;

    protected array $where_fields = [];
    protected array $order_by_fields = [];
    protected array $select_fields = [];

    protected ?flcJoins $joins = null;

    /**
     * Reset all the constraints values for reuse the instance.
     *
     * @return void
     */
    public function reset() : void {
        $this->start_row = 0;
        $this->end_row   = 0;

        $this->where_fields = [];
        $this->order_by_fields = [];
        $this->select_fields = [];

        $this->joins = null;
    }

    /**
     * Returns the first row in the resultset to lookup
     * in pagination.
     *
     * @return int
     */
    public function get_start_row(): int {
        return $this->start_row;
    }

    /**
     * Set the first row in the resultset to lookup
     * in pagination.
     *
     * @param int $p_start_row the row number
     */
    public function set_start_row(int $p_start_row): void {
        $this->start_row = $p_start_row;
    }

    /**
     * Returns the last row in the resultset to lookup
     * in pagination.
     *
     * @return int
     */
    public function get_end_row(): int {
        return $this->end_row;
    }

    /**
     * Set the first row in the resultset to lookup
     * in pagination.
     *
     * @param int $p_end_row the row number
     */
    public function set_end_row(int $p_end_row): void {
        $this->end_row = $p_end_row;
    }

    /**
     * Set the where fields to use.
     * Each array entry can be :
     *  - A field name only.
     *  - An array with the field name and the operator.
     *  - An array with the field name , the operator and the value.
     *
     * Example :
 *              [['field1','>'],'field2','field3',['field4','like]]
     *
     * if some field will be used in conjuction with a join an came from another table this need to be defined
     * as :
     *          [['table_name.field_name']].
     *
     * Operators allowed are :
     * - '='
     * - '!='
     * - '>'
     * - '>='
     * - '<'
     * - '<='
     * - 'like'  - equivalent for like '%fieldx%'
     * - 'ilike' - same but case insensitive
     * - 'like(%-)'  - equivalent for like '%fieldx'
     * - 'like(-%)'  - equivalent for like 'fieldx%'
     * - 'ilike(%-)'  - equivalent to like(%-) but case insensitive
     * - 'ilike(-%)'  - equivalent like(-%) but case insensitive
     * - 'nlike'  - equivalent for not like '%fieldx%'
     * - 'nilike' - same but case insensitive
     * - 'nlike(%-)'  - equivalent for not like '%fieldx'
     * - 'nlike(-%)'  - equivalent for not like 'fieldx%'
     * - 'nilike(%-)'  - equivalent to nlike(%-) but case insensitive
     * - 'nilike(-%)'  - equivalent nilike(-%) but case insensitive
     *
     * if an array entry only define the field name the '=' operator will be used.
     *
     *
     * @param array $p_where_fields array with the where fields-
     *
     * @return void
     */
    public function set_where_fields(array $p_where_fields) {
        foreach ($p_where_fields as $field) {
            $this->add_where_field($field);
        }
    }

    public function add_where_field(array $p_where_field) {
        if (!in_array($p_where_field[1], [
            '=',
            '!=',
            '>',
            '>=',
            '<',
            '<=',
            'like',
            'ilike',
            'like(%-)',
            'like(-%)',
            'ilike(%-)',
            'ilike(-%)',
            'nlike',
            'nilike',
            'nlike(%-)',
            'nlike(-%)',
            'nilike(%-)',
            'nilike(-%)'
        ])) {
            throw new InvalidArgumentException('Where fields operators allowed =,!=,>,>=,<,<=,like,ilike,nlike,niike,etc , see documentation');
        }

        $this->where_fields[] = $p_where_field;
    }

    /**
     * Return the where fields to use.
     *
     * @return array of string with the field names to use in where condition.
     */
    public function get_where_fields(): array {
        return $this->where_fields;
    }

    /**
     * Set the order by fields to use.
     *
     * @param array $p_order_by_fields array of string with the field names to use in order by condition.
     *
     * @return void
     */
    public function set_order_by_fields(array $p_order_by_fields) {
        $this->order_by_fields = $p_order_by_fields;
    }

    /**
     * Return the order by fields to use.
     *
     * @return array of string with the field names to use in the order by condition.
     */
    public function get_order_by_fields(): array {
        return $this->order_by_fields;
    }

    /**
     * Set the fields to select , used in fetch operations.
     *
     * @param array $p_select_fields array of string with the field names to use in
     * fields to use in select.
     *
     * @return void
     */
    public function set_select_fields(array $p_select_fields) {
        $this->select_fields = $p_select_fields;
    }

    /**
     * Return the fields to use in select.
     *
     * @return array with the field names to use in fields to use in select.
     */
    public function get_select_fields(): array {
        return $this->select_fields;
    }


    /**
     * Set the joins to be used as constraint
     *
     * @param flcJoins $p_joins
     *
     * @return void
     */
    public function set_joins(flcJoins $p_joins) {
        $this->joins = $p_joins;
    }

    /**
     * Return the current joins defined in the constraints.
     *
     * @return flcJoins|null
     */
    public function get_joins(): ?flcJoins {
        return $this->joins;
    }

}