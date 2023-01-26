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

use framework\core\accessor\constraints\flcConstraints;
use framework\core\entity\flcBaseEntity;
use InvalidArgumentException;

/**
 * Class that will be used as glue between the external source of data and the entity/
 * model.
 *
 * This class need to be override to implement the process_input_data method and conver external
 * data to internal data of this class. (flcInputDataProcessor)
 *
 * This class after processed the input can be consumed by an instance of flcInpitProcessor.
 */
abstract class flcInputDataProcessor {
    /**
     * The operation to do over the model/entity ,
     * can be:
     *      add
     *      del
     *      fetch
     *      read
     *      upd
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

    /*--------------------------------------------------------------*/

    /**
     * on fetch , first row to return (pagination)
     * @var int
     */
    protected int $start_row = 0;

    /*--------------------------------------------------------------*/

    /**
     * on fetch , last row to return (pagination)
     * @var int
     */
    protected int $end_row = 0;

    /*--------------------------------------------------------------*/

    /**
     * Array of fields part of the model/entity
     * this array is a pair of :
     *
     * fieldname => value.
     *
     * @var array
     */
    protected array $fields = [];

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

    /**
     * Hold the array of input values to process
     * @var array
     */
    protected array $input_data;

    /*--------------------------------------------------------------*/

    /**
     * Constructor.
     *
     * @param array $p_input_data
     *
     * @see this::set_input_data
     */
    public function __construct(?array $p_input_data) {
        $this->input_data = $p_input_data;
    }


    /**
     * Overridable method to take an array of input data to parse
     * and conver to normalized format supported by this class.
     * The input array can be a $_POST array by example.
     *
     * @param flcBaseEntity $p_entity used for get the valid model/entity fields to extract from the input data array
     *
     * @throws InvalidArgumentException if the input data have some problems
     * or it cant be parsed.
     */
    public abstract function process_input_data(flcBaseEntity $p_entity);

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
    public function get_fields(): array {
        return $this->fields;
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
    public function get_filter_fields(): array {
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

    /**
     * IF the input data contains required values for create constriants for the
     * operation , assembly the base constraints.
     *
     * @param string $p_operation
     *
     * @return flcConstraints|null
     */
    public function &get_constraints(string $p_operation): ?flcConstraints {
        $c = null;

        switch ($p_operation) {
            case 'fetch':
                $c = new flcConstraints();
                $fields = [];

                // where fields
                foreach ($this->get_filter_fields() as $field => $operator) {
                    $fields[] = [$field, $operator];
                }
                if (count($fields) > 0) {
                    $c->set_where_fields($fields);
                }

                // sort fields
                $fields = [];
                foreach ($this->get_sort_fields() as $sfield) {
                    $fields[] = $sfield;
                }
                if (count($fields) > 0) {
                    $c->set_order_by_fields($fields);
                }

                // pagination
                $start_row = $this->get_start_row();
                $end_row = $this->get_end_row();

                if ($end_row - $start_row > 0) {
                    $c->set_start_row($start_row);
                    $c->set_end_row($end_row);
                } else {
                    $c->set_start_row(0);
                    $c->set_end_row(0);
                }
                break;

            case 'delete':
                $c = new flcConstraints();
                $fields = [];

                // where fields
                foreach ($this->get_filter_fields() as $field => $operator) {
                    $fields[] = [$field, $operator];
                }
                if (count($fields) > 0) {
                    $c->set_where_fields($fields);
                }
                break;

        }

        return $c;
    }

    /*--------------------------------------------------------------*/

}