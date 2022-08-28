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
 * Database Result Class
 *
 * This is the platform-independent output parameters result class.
 *
 * @category    Database
 * @author       Carlos Arana Reategui
 * @link        https://flabscorpprods.com
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
