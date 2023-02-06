<?php

/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 *
 */

namespace flc\core\dto;


/**
 * Class that will process the output data obtained from a cliente query and conver to
 * something that the client will understand.
 *
 * This class need to be override to implement the process_output_data method
 *
 */
abstract class flcOutputDataProcessor {

    /**
     * Overridable method to take an flcOutputData instance,  parse
     * and convert to normalized format supported by the client.
     *
     * @param flcOutputData $p_output_data
     *
     * @return string|array , can be an array , a json string , and xml ,etc required
     * by an specific client.
     *
     */
    public abstract function process_output_data(flcOutputData $p_output_data);


    /*--------------------------------------------------------------*/

}