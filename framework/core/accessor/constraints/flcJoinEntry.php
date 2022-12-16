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
 * Defined each join between 2 tables
 */
class flcJoinEntry {
    public static string $INNER_JOIN = 'INNER JOIN';
    public static string $LEFT_JOIN  = 'LEFT JOIN';
    public static string $RIGHT_JOIN = 'RIGHT JOIN';

    protected string $table;
    protected string $ref_table;
    protected array  $fields = [];
    protected array  $show_fields = [];
    protected string $join_type;

    /**
     * @param string      $p_table the table name
     * @param string      $p_ref_table the joined table name
     * @param array       $p_fields of fields that join the tables ex: ['field from $p_table' => 'field from $p_ref_table,..]
     * @param array       $p_show_fields of fields from $p_table
     * @param string|null $p_type if it is left join,inner join , right join , for easy definition
     * use flcJoinEbtry::$INNER_JOIN , flcJoinEntry::$LEFT_JOIM, flcJoinEntry::$RIGHT_JOIM
     *
     * @return void
     */
    public function initialize(string $p_table, string $p_ref_table, array $p_fields, array $p_show_fields, ?string $p_type = null) {
        $this->table = $p_table;
        $this->ref_table = $p_ref_table;
        $this->fields = $p_fields;
        $this->show_fields = $p_show_fields;

        if ($p_type == null || $p_type != self::$LEFT_JOIN || $p_type != self::$RIGHT_JOIN) {
            $this->join_type = self::$INNER_JOIN;
        } else {
            $this->join_type = $p_type;
        }

        if (!$this->validate()) {
            throw new InvalidArgumentException('Join definition is invalid');
        }
    }


    /**
     * Return the join string  based on the values defined in initialize
     *
     * @return string the join string
     *
     * @todo support more the and operator in the join.
     */
    public function get_join_str(): string {
        if (!$this->validate()) {
            throw new InvalidArgumentException('Join definition is invalid');
        }

        $sql = $this->join_type." $this->table on ";
        foreach ($this->fields as $field => $field_ref) {
            $sql .= "$this->table.$field = $this->ref_table.$field_ref and ";
        }

        return substr($sql,0,strrpos($sql,' and '));
    }

    /**
     * Return the part of the clause to be added to the select part of the query
     *
     * @return string
     */
    public function get_joined_fields_str(): string {
        if (!$this->validate()) {
            throw new InvalidArgumentException('Join definition is invalid');
        }

        $sql = '';
        foreach ($this->show_fields as $field) {
            $sql .= "$this->table.$field,";
        }

        return substr($sql,0,strrpos($sql,','));

    }

    /**
     * Validate if all the values required to do the joins are defined.
     * OF course this will happen if initialize is not called o is called with
     * invalid arguments.
     *
     * @return bool if its ok
     */
    public function validate() : bool {
        if (empty($this->table) || empty($this->ref_table)) {
            return false;
        }

        if (count($this->fields) == 0 || count($this->show_fields) == 0) {
            return false;
        }

        foreach ($this->fields as $field) {
            if (!is_string($field)) {
                return false;
            }
        }

        foreach ($this->show_fields as $field) {
            if (!is_string($field)) {
                return false;
            }
        }

        if (empty($this->join_type)) {
            return false;
        }

        return true;
    }
}