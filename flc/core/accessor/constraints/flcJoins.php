<?php
/**
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 */

namespace flc\core\accessor\constraints;

/**
 * Class that defines a group of joins to be used in a select clause.
 */
class flcJoins {
    /**
     * @var array
     */
    private array $joins = [];

    /**
     * Add a join entry
     *
     * @param flcJoinEntry $p_join
     *
     * @return void
     */
    public function add_join(flcJoinEntry $p_join) {
        $this->joins[] = $p_join;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the number of joins defined
     * @return int
     */
    public function num_joins(): int {
        return count($this->joins);
    }

    /*--------------------------------------------------------------*/

    /**
     * @return array of flcJoinEntry
     */
    public function get_joins() : array {
        return $this->joins;
    }

    /*--------------------------------------------------------------*/

    /**
     * Get all joins based on each join entry.
     *
     * @return string
     */
    public function get_join_string(): string {
        $sql = '';
        if (count($this->joins) > 0) {
            /**
             * @var flcJoinEntry $join
             */
            foreach ($this->joins as $join) {
                $sql .= ' '.$join->get_join_str().PHP_EOL;
            }

        }

        return $sql;
    }

    /*--------------------------------------------------------------*/

    /**
     * Concatenate all the fields joined.
     *
     * @return string
     */
    public function get_join_fields_string(): string {
        $sql = '';
        if (count($this->joins) > 0) {
            /**
             * @var flcJoinEntry $join
             */
            foreach ($this->joins as $join) {
                $sql .= $join->get_joined_fields_str().',';
            }


            $sql = substr($sql,0,strrpos($sql,','));
        }

        return $sql;
    }

    /*--------------------------------------------------------------*/

}