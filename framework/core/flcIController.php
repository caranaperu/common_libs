<?php
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

namespace framework\core;

use Exception;


/**
 * Controller interface
 * Define the required method to implement by the final
 * real controllers.
 */
interface flcIController {

    /**
     * Initialize the class , if this method is overload always
     * call the parent class.
     *
     * @return void
     * @throws Exception
     *
     */
    public function initialize();

    // --------------------------------------------------------------------

    /**
     * Called by the bootstrap before call the index method.
     *
     * @return void
     * @throws Exception
     *
     */
    public function pre_index();

    // --------------------------------------------------------------------

    /**
     * Called by the bootstrap after call the index method.
     *
     * @return void
     * @throws Exception
     *
     */
    public function post_index();

    // --------------------------------------------------------------------

    /**
     * The entry point for the controller , this will be called
     * by the bootstrap.
     *
     * @return void
     * @throws Exception
     *
     */
    public  function index();
}
