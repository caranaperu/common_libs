<?php
/**
 * This file is part of Future Labs Code 1 framework.
 **
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 */

namespace flc\core\accessor;

use Throwable;

/**
 * Class that defines the answer thar returns from a data accessor.
 *
 */
class flcPersistenceAccessorAnswer {
    protected bool $success = false;

    protected Throwable $ex;

    protected int $return_code;

    protected array $result_array;

    /**
     * Set if the operation is success.
     *
     * @param bool $p_is_success true if success.
     *
     * @return void
     */
    public function set_success(bool $p_is_success) {
        $this->success = $p_is_success;
    }

    /**
     * Return if the operation is success.
     *
     * @return bool true if success
     */
    public function is_success(): bool {
        return $this->success;
    }

    /**
     * Set the return code , if is_success() is true means that this code
     * contains an error code.
     * @param int $p_return_code
     *
     * @return void
     */
    public function set_return_code(int $p_return_code) {
        $this->return_code = $p_return_code;
    }

    /**
     * Returns the return code , if is_success() is true means that this code
     * contains an error code.
     *
     * @return int the error code
     */
    public function get_return_code(): int {
        return $this->return_code;
    }


    /**
     * Set the exception generated from the accessor , this can only be set
     * if is_success() is false.
     *
     * @param Throwable $p_exception
     *
     * @return void
     */
    public function set_exception(Throwable $p_exception) {
        $this->ex = $p_exception;
    }

    /**
     * Get the exception generated from the accessor , this can only be set
     * if is_success() is false.
     *
     * @return Throwable , null if is_success() is false.
     */
    public function get_exception(): ?Throwable {
        return $this->ex ?? null;
    }

    /**
     * Set the results array , is used when the answer contains a lot of
     * records like fetch, in this scenario we will not map to model classes
     * beause consumes a lot of memory.
     *
     * This only can called when is_success() is true and the operation can return
     *  multiple rows.
     *
     * @param array $p_results
     *
     * @return void
     */
    public function set_result_array(array $p_results) {
        $this->result_array = $p_results;
    }

    /**
     * Get the results array.
     *
     * This only can called when is_success() is true and the operation can return
     *  multiple rows.
     *
     * @return array with the array containing the results
     */
    public function get_result_array() : array  {
        return $this->result_array;
    }
}