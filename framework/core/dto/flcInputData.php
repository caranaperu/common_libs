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

namespace framework\core\dto;

use InvalidArgumentException;

/**
 * Class that will be used as glue between the external source of data and the entity
 * model.
 * This class need to override to implement the process_input_data method and conver external
 * data to internal data of this class.
 */
abstract class flcInputData {
    /**
     * The operation to do over the model/entity ,
     * can be:
     *      add
     *      delete or remove
     *      fetch
     *      update
     *
     * @var string
     */
    protected string $operation = '';

    /*--------------------------------------------------------------*/

    /**
     * string with a string that defines some sub operation
     * that will be interpreted in the model operations.
     *
     * @var string
     */
    protected string $sub_operation = '';

    /*--------------------------------------------------------------*/

    /**
     * The user that is doing the request to the model
     *
     * @var string
     */
    protected string $session_user = '';
    protected int    $start_row    = 0;
    protected int    $end_row      = 0;

    /*--------------------------------------------------------------*/

    /**
     * Array of fields part of the model/entity
     * this array is a pair of :
     *
     * fieldname => value.
     *
     * @var array
     */
    protected array $model_fields = [];

    /*--------------------------------------------------------------*/

    /**
     * Array of fieldname => sort direction ,
     * the sort direction can be desc or asc
     *
     * @var array
     */
    protected array $sort_fields = [];

    /*--------------------------------------------------------------*/

    /**
     * Array of fields to be used as filter on the persistence.
     * This array can take the form of :
     *
     * fieldname => operator
     *
     * @var array
     */
    protected array $filter_fields = [];

    /*--------------------------------------------------------------*/

    public function __construct(array $p_input_data) {
        $this->process_input_data($p_input_data);
    }

    /**
     * Overridable method to take an array of input data to parse
     * and conver to normalized format supported by this class.
     * The input array can be a $_POST array by example.
     *
     * @param array $p_input_data
     *
     * @throws InvalidArgumentException if the input data have some problems
     * or it cant be parsed.
     *
     */
    public abstract function process_input_data(array $p_input_data);

    /*--------------------------------------------------------------*/

    /**
     *
     * Return the operation to do over the model , as 'add','update',
     * 'read','delete',fetch
     *
     * @return string
     */
    public function get_operation(): string {
        return $this->operation;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the sub operation to do over the model , is of free use
     * and can be interpreted in the entity/model class.
     * @return string
     */
    public function get_sub_operation(): string {
        return $this->sub_operation;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the start row to be used in pagination.
     * @return int
     */
    public function get_start_row(): int {
        return $this->start_row;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the end row to be used in pagination.
     * @return int
     */
    public function get_end_row(): int {
        return $this->end_row;
    }

    /*--------------------------------------------------------------*/

    /**
     * Array of fieldname => value pairs with the fields that correspond
     * to the model to use.
     *
     * @return array
     */
    public function get_model_fields(): array {
        return $this->model_fields;
    }

    /*--------------------------------------------------------------*/

    /**
     * Array of fields to be used as filter on the persistence.
     * This array can take the form of :
     *
     * fieldname => operator
     *
     * @return array
     */
    public function get_filter_fields() : array {
        return $this->filter_fields;
    }

    /*--------------------------------------------------------------*/


    /**
     * Array of fieldname => direction pairs with the fields
     * to be used to sort results obtained from the persistence.
     *
     * The direction need to be 'asc' or 'desc'
     *
     * @return array
     */
    public function get_sort_fields(): array {
        return $this->sort_fields;
    }

    /*--------------------------------------------------------------*/

}