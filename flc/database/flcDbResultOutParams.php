<?php

namespace flc\database;

/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 *
 * @author Carlos Arana Reategui.
 *
 */

/**
 * Database Result Class
 *
 * This is the platform-independent output parameters result class.
 *
 */
class flcDbResultOutParams {

    /**
     * Array of output parameters
     *
     * @var  array
     */
    private array $outparams = [];

    // --------------------------------------------------------------------

    /**
     * The number of parameters
     * @var int
     */
    private int $num_params = 0;

    // --------------------------------------------------------------------

    /**
     * Number of rows in the result set, if num_rows is already defined return
     * immediately otherwise search which array has answers, otherwise go to the db
     *
     * @return    int
     */
    public function num_params(): int {
        if ($this->num_params > 0) {
            return $this->num_params;
        }

        return $this->num_params = count($this->outparams);
    }

    // --------------------------------------------------------------------

    /**
     * Add an output  parameter value to the list of results.
     *
     * @param string $p_param_name
     * @param        $p_param_value
     *
     * @return void
     */
    public function add_out_param(string $p_param_name, $p_param_value) {
        $this->outparams[$p_param_name] = $p_param_value;
    }

    // --------------------------------------------------------------------

    /**
     * Return an array with the total of output parameters.
     *
     * @return array
     */
    public function get_out_params(): array {
        return $this->outparams;
    }

}
