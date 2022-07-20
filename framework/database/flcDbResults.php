<?php

namespace framework\database;

/**
 * FLabsCode
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2022 - 2022, Future Labs Corp-
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    FLabsCode
 * @author    Carlos Arana
 * @copyright    Copyright (c) 2022 - 2022, FLabsCode
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://flabscorpprods.com
 * @since    Version 1.0.0
 * @filesource
 */

/**
 * Database Results Class
 *
 * This is the platform-independent result class.
 * This class will  be called directly if not specific adapter
 * class for the specific database exist.
 *
 * Important : This class is a modified one of db_result from codeigniter
 * all credits for his authors.
 *
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
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
     * @var int
     */
    private flcDbResultOutParams $outparams;


    // --------------------------------------------------------------------

    /**
     * @param object $p_resulset_id
     *
     * @return void
     */
    public function add_resultset_result(flcDbResult $p_resulset_result) {
        if (isset($p_resulset_result)) {
            $this->resultsets[] = $p_resulset_result;
        }
    }

    // --------------------------------------------------------------------


    public function add_outparams_result(flcDbResultOutParams $p_outparams_result) {
        $this->outparams = $p_outparams_result;
    }

    // --------------------------------------------------------------------

    public function get_resultset_result(int $index = 0): ?flcDbResult {
        if (isset($this->resultsets[$index])) {
            return $this->resultsets[$index];
        }

        return null;
    }

    public function resultset_free_result(int $index = 0) {
        if (isset($this->resultsets[$index])) {
            $this->resultsets[$index]->free_result();
            unset($this->resultsets[$index]);
        }
    }

    public function get_num_resultsets() : int {
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
