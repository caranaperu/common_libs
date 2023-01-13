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

namespace framework\core\dto;

use framework\core\entity\flcBaseEntity;
use Throwable;

/**
 * Class that defines the required data to create a response to the client .
 * This can be process errors , validation errors , parameters , messages or the output data.
 *
 * This class can be consumed by am flcOutputProcessor instance.
 *
 */
class flcOutputData {

    /**
     * Array of process errors
     *
     * The format is :  [error code,error msg,exception]
     *
     * @var array $process_errors
     */
    private array $process_errors;

    /*--------------------------------------------------------------*/

    /**
     * Array of field or parameter validation errors.
     *
     * The format is : fieldname=>errormsg
     *
     * @var array $field_errors
     */
    private array $field_errors;

    /*--------------------------------------------------------------*/

    /**
     * True if all went ok , in this case only expect output data.
     *
     * @var boolean $m_success
     */
    private bool $success = true;

    /*--------------------------------------------------------------*/

    /**
     * Optional answer message.
     * @var string $answer_message
     */
    private string $answer_message;

    /*--------------------------------------------------------------*/

    /**
     * If $answer_message is a message error , this error code need to have a
     * error code.
     * @var int $error_code
     */
    private int $error_code = 0;

    /*--------------------------------------------------------------*/

    /**
     * The output data of all went ok.
     * .
     * @var array|flcBaseEntity $result_data
     */
    private $result_data;

    /*--------------------------------------------------------------*/

    /**
     * Extra data array like number of records, last record readed, or anything
     * required.
     * @var array $out_params
     */
    private array $out_params=[];

    /*--------------------------------------------------------------*/

    /**
     * first row selected (pagination)
     * @var int
     */
    protected int $start_row = 0;

    /*--------------------------------------------------------------*/

    /**
     * last row selected (pagination)
     * @var int
     */
    protected int $end_row = 0;

    /*--------------------------------------------------------------*/

    /**
     *
     * Set if success or not , if its false we expect some kind of error ,
     * process , field validation , etc.
     *
     * @param bool $success
     */
    public function set_success(bool $success): void {
        $this->success = $success;
    }

    /*--------------------------------------------------------------*/

    /**
     * Add an process error , each entry is an array of :
     *
     * [error code, error message,exception]
     *
     * @param int            $p_error_code error code , need to be > 0
     * @param string|null    $p_error_message the error message
     * @param Throwable|null $p_exception An exception if its the source of the problem
     *
     * @return void
     */
    public function add_process_error(int $p_error_code, ?string $p_error_message = null, ?Throwable $p_exception = null): void {
        // if are errors , obvious not success
        $this->set_success(false);
        $this->process_errors[] = [$p_error_code, $p_error_message ?? '', $p_exception ?? ''];
    }

    /*--------------------------------------------------------------*/

    /**
     * Add an field error . basically came from validation errors on fields or parameters.
     *
     * @param string $p_field the field name
     * @param string $p_error_message the associated error message
     */
    public function add_field_error(string $p_field, string $p_error_message): void {
        // if are errors , obvious not success
        $this->set_success(false);
        $this->field_errors[$p_field] = $p_error_message;
    }

    /*--------------------------------------------------------------*/

    /**
     * Add an output parameter value.
     *
     * @param string $p_param_name name of parameter.
     * @param mixed  $p_param_value his value
     */
    public function add_output_parameter(string $p_param_name, $p_param_value) {
        $this->out_params[$p_param_name] = $p_param_value;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the array of output parameters if exist.
     * The format is 'param name' => value
     *
     * @return array with output parameters
     */
    public function get_output_parameters(): ?array {
        return $this->out_params;
    }

    /*--------------------------------------------------------------*/

    /**
     * When an operation is ok , the response data will be passed to this method.
     *
     * The data will be interpreted by context from an outside function.
     *
     * @param array|flcBaseEntity $p_result_data los datos resultado.
     */
    public function set_result_data($p_result_data): void {
        if (isset ($p_result_data)) {
            $this->result_data = $p_result_data;
        }
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the response data.
     *
     * @return array|flcBaseEntity|null con los resultados.
     */
    public function get_result_data() {
        return $this->result_data;
    }

    /*--------------------------------------------------------------*/

    /**
     * Clear the response data.
     */
    public function clear_result_data(): void {
        unset($this->result_data);
        $this->result_data = null;
    }

    /*--------------------------------------------------------------*/

    /**
     *
     * If its required send a message when its not a process error or validation
     * error , like 'Operation finished ok' or something like that cant be added here.
     *
     * Also if the error came from an exception or require an special code like an
     * SQL ERROR CODE can be indicated in the second parameter.
     *
     * @param string $p_error_message el mensaje de salida
     * @param int    $p_error_code codigo de error.
     */
    public function set_answer_message(string $p_error_message, int $p_error_code) {
        if (strlen($p_error_message) > 0) {
            // prepare for json or xml
            $this->answer_message = str_replace(["\"", "\r", "\n", "\r\n"], ' ', $p_error_message);
            $this->error_code = $p_error_code;
        }
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the output message.
     *
     * @return String with the message.
     */
    public function get_answer_message(): string {
        if (isset($this->answer_message) && !empty($this->answer_message)) {
            return $this->answer_message;
        } else {
            return '';
        }
    }

    /*--------------------------------------------------------------*/

    /**
     * Retorna el codigo de error global.
     * @return int con el codigo de error
     */
    public function get_error_code(): int {
        return $this->error_code;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return the process errors , each entry ;
     *
     *[error code, error message,exception]
     *
     * @return array with the list of process errors..
     */
    public function get_process_errors(): array {
        return $this->process_errors;
    }

    /*--------------------------------------------------------------*/

    /**
     * Set the first row in the resultset used to lookup
     * in pagination.
     *
     * @param int $p_start_row the row number
     */
    public function set_start_row(int $p_start_row): void {
        $this->start_row = $p_start_row;
    }

    /*--------------------------------------------------------------*/

    /**
     * Set the first row in the resultset used to lookup
     * in pagination.
     *
     * @param int $p_end_row the row number
     */
    public function set_end_row(int $p_end_row): void {
        $this->end_row = $p_end_row;
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
     * Return the list of each validation or parameters error messages,
     * the format of each entry ;
     *
     * field => msg
     *
     * @return array  with the messages.
     */
    public function get_field_errors(): array {
        return $this->field_errors;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return if this instance have process error messages.
     *
     * @return bool
     */
    public function has_process_errors(): bool {
        if (isset($this->process_errors) && count($this->process_errors) > 0) {
            return true;
        }

        return false;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return if this instance have field error messages.
     *
     * @return bool
     */
    public function has_field_errors(): bool {
        if (isset($this->field_errors) && count($this->field_errors) > 0) {
            return true;
        }

        return false;
    }

    /*--------------------------------------------------------------*/

    /**
     * Return if the operation was ok.
     *
     * @return bool
     */
    public function is_success(): bool {
        return $this->success;
    }

    /*--------------------------------------------------------------*/

}