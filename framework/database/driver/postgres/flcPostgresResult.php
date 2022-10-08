<?php

namespace framework\database\driver\postgres;

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

use framework\database\flcDbResult;
use stdClass;


/**
 * Postgres Result Class
 *
 * This class extends the parent result class: \framework\database\flcDbResult
 * and is the specific one for postgresql.
 *
 * Important : This class is a modified one of postgre_result from codeigniter
 * all credits for his authors.
 */
class flcPostgresResult extends flcDbResult {

    /**
     * @inheritdoc
     */
    public function num_rows(): int {
        return ($this->num_rows > 0) ? $this->num_rows : $this->num_rows = pg_num_rows($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function num_fields(): int {
        return pg_num_fields($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function list_fields(): array {
        $field_names = [];
        for ($i = 0, $c = $this->num_fields(); $i < $c; $i++) {
            $field_names[] = pg_field_name($this->result_id, $i);
        }

        return $field_names;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function field_data(): array {
        $retval = [];
        for ($i = 0, $c = $this->num_fields(); $i < $c; $i++) {
            $retval[$i] = new stdClass();
            $retval[$i]->name = pg_field_name($this->result_id, $i);
            $retval[$i]->type = pg_field_type($this->result_id, $i);
            $retval[$i]->max_length = pg_field_size($this->result_id, $i);
        }

        return $retval;
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function free_result(): void {
        if (is_resource($this->result_id)) {
            pg_free_result($this->result_id);
            $this->result_id = null;
        }
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function data_seek(int $p_nrecord = 0): bool {
        return pg_result_seek($this->result_id, $p_nrecord);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _fetch_assoc() {
        return pg_fetch_assoc($this->result_id);
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    protected function _fetch_object(string $p_classname = 'stdClass'): ?object {
        $obj = pg_fetch_object($this->result_id, NULL, $p_classname);

        return is_object($obj) ? $obj : null;
    }


    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function affected_rows() : int {
        return pg_affected_rows($this->result_id);
    }

}
