<?php

namespace flc\database;

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

/**
 * Database Multiple Results Class
 *
 * This is the platform-independent multiple result class.
 * This class is a container of a multiple resulset(s) and output
 * parameters to support stored procedures / function that support
 * the return of multiple resulsets and also output parameters.
 *
 */
class flcDbResults {

    /**
     * Array of resultsets
     *
     * @var    array
     */
    private array $resultsets = [];

    // --------------------------------------------------------------------

    /**
     * The number of parameters
     * @var flcDbResultOutParams
     */
    private flcDbResultOutParams $outparams;


    // --------------------------------------------------------------------

    /**
     * Add a resultset results to the list of resultsets.
     *
     * @param flcDbResult $p_resulset_result
     *
     * @return void
     */
    public function add_resultset_result(flcDbResult $p_resulset_result) {
        $this->resultsets[] = $p_resulset_result;
    }

    // --------------------------------------------------------------------


    /**
     * Add the  results that contains the output parameters.
     *
     * @param flcDbResultOutParams $p_outparams_result
     *
     * @return void
     */
    public function add_outparams_result(flcDbResultOutParams $p_outparams_result) {
        $this->outparams = $p_outparams_result;
    }

    // --------------------------------------------------------------------

    /**
     * Return the resultset in the index position or null if not exist.
     * We dont use $this->resultsets[$index] because some of the elements can be removed
     * and the index will be different that the position.
     *
     * @param int $index number of element to search
     *
     * @return flcDbResult|null the result
     */
    public function get_resultset_result(int $index = 0): ?flcDbResult {
        if (isset($this->resultsets)) {
            $result = array_values($this->resultsets)[$index];
            if (isset($result)) {
                return $result;
            }

        }

        return null;
    }

    // --------------------------------------------------------------------

    /**
     * Free the resources asociated to a resultset
     *
     * @param int $index
     *
     * @return void
     */
    public function resultset_free_result(int $index = 0) {
        if (isset($this->resultsets[$index])) {
            $this->resultsets[$index]->free_result();
            unset($this->resultsets[$index]);
        }
    }

    // --------------------------------------------------------------------


    /**
     * Return the number of resultsets
     *
     * @return int
     */
    public function get_num_resultsets(): int {
        return count($this->resultsets);
    }

    // --------------------------------------------------------------------

    public function get_out_params(): ?flcDbResultOutParams {
        if (isset($this->outparams)) {
            return $this->outparams;
        }

        return null;
    }

}
