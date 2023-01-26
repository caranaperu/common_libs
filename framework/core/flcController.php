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


use Throwable;

/**
 * Controller abstract class, define the methods that neeed to be override to
 * manipulate the request.
 *
 * Is important to know , the final real controller classes can define the output
 * method , with the signature output($string $buffer), if its defined will be called
 * by the bootstrap to send to the controller the output buffer , and this method
 * will be the responsible to send to browser.
 */
abstract class flcController {

    /**
     * We need to check if the user is logged in?
     *
     * @var bool
     */
    protected bool $check_logged_in = false;

    /**
     * Default key for check if it is logged in
     *
     * @var string
     */
    protected string $logged_in_key = 'is_logged_in';

    // --------------------------------------------------------------------

    /**
     * Initialize the class , if this method is overload always
     * call the parent class.
     *
     * @return void
     * @throws Throwable
     *
     */
    public function initialize() {
        // TODO: Implement initialize() method.
    }

    // --------------------------------------------------------------------

    /**
     * Called by the bootstrap before call the index method.
     *
     * @return void
     * @throws Throwable
     *
     */
    public function pre_index() {
        // TODO: Implement pre_index() method.
    }

    // --------------------------------------------------------------------

    /**
     * Called by the bootstrap after call the index method.
     *
     * @return void
     * @throws Throwable
     *
     */
    public function post_index() {
        // TODO: Implement post_index() method.
    }

    // --------------------------------------------------------------------

    /**
     * The entry point for the controller , this will be called
     * by the bootstrap, all the important logic need to be there.
     *
     * @return void
     * @throws Throwable
     *
     */
    public abstract function index();

    // --------------------------------------------------------------------

    /**
     * Check if the user is logged in , can be on a session or other methods.
     *
     * @return bool true is logged
     * @throws Throwable
     */
    public abstract function is_logged_in(): bool;

    // --------------------------------------------------------------------

    /**
     * Set if the uer is loggged in or not , left the implementation
     * to the implementation classes.
     *
     * @param bool $p_is_loggedd_in
     *
     * @return void
     * @throws Throwable
     */
    public abstract function set_logged_in(bool $p_is_loggedd_in): void;

    // --------------------------------------------------------------------

}
