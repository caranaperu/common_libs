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


/**
 * Controller abstract class, define the methods that neeed to be override to
 * manipulate the request.
 *
 * Is important to know , the final real controller classes can define the output
 * method , with the signature output($string $buffer), if its defined will be called
 * by the bootstrap to send to the controller the output buffer , and this method
 * will be the responsible to send to browser.
 */
abstract class flcController implements flcIController {

    /**
     * @inheritdoc
     */
    public function initialize() {
        // TODO: Implement initialize() method.
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function pre_index() {
        // TODO: Implement pre_index() method.
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function post_index() {
        // TODO: Implement post_index() method.
    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public abstract function index();

    // --------------------------------------------------------------------

}
