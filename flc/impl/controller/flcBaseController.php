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

namespace flc\impl\controller;

use Exception;
use flc\core\accessor\flcDbAccessor;
use flc\core\accessor\flcPersistenceAccessorAnswer;
use flc\core\model\flcBaseModel;
use flc\core\FLC;
use flc\core\flcController;
use flc\core\flcValidation;
use flc\flcCommon;
use RuntimeException;
use Throwable;

/**
 * Base controller , manage the default flow for all client hits.
 *
 */
abstract class flcBaseController extends flcController {
    use flcControllerHelperTrait;

    protected flcBaseModel $model;


    public function __construct() {
        @set_exception_handler([
            $this,
            'controller_exception_handler',
        ]);

    }

    // --------------------------------------------------------------------

    /**
     * @inheritdoc
     *
     * call to init options
     */
    public function initialize() {
        parent::initialize();
        $this->init_options();
    }

    // --------------------------------------------------------------------

    /**
     * Execute the validation on the data, this method use the validation language file
     * defined in options using the current default language.
     *
     * In case of errors , each error is added to the output data.
     *
     * @param string $p_operation the operation , ie : add, del , etc
     * @param array  $p_data the data to validate
     *
     * @return bool true means no errors
     * @throws Exception
     */
    protected function execute_validations(string $p_operation, array $p_data): bool {
        if ($this->options && count($this->options) && isset($this->options['validation'])) {
            // no validations defined
            $flc = FLC::get_instance();
            if (!$flc->lang->load($this->options['language_file'] ?? 'unknown')) {
                throw new RuntimeException('Invalid language file '.($this->options['language_file'] ?? 'unknown').' - BC00007');
            }

            // validate only if its defined a validation entry for the operation
            $validoper = $this->options['validation'][$p_operation] ?? null;
            // if its null then  means mo validations for the operation
            if ($validoper !== null) {
                $flc->set_validations($validoper['file']);
                $rules = $flc->get_rules($validoper['group'], $validoper['rules']);
                // search in the rules if exist a field with the name _rowversion_field , and if exist modify to
                // real rowversion field supported by the table
                for ($i=0; $i < count($rules); $i++) {
                    if ($rules[$i]['field'] == '_rowversion_field') {
                        $rules[$i]['field'] = $this->model->get_rowversion_field();
                    }
                }

                $flc->validation = new flcValidation($flc->lang);
                $flc->validation->set_rules($rules);
                $flc->validation->set_data($p_data);

                // do validation
                if (!$flc->validation->run()) {
                    // get errors ant put on output data
                    $errors = $flc->validation->error_array();
                    foreach ($errors as $field => $msg) {
                        $this->output_data->add_field_error($field, $msg);

                    }

                    return false;
                }
            }
        }

        return true;
    }


    // --------------------------------------------------------------------

    /**
     * The default main entry .
     * - check if a user is logged in (depends on a property)
     * - do validations
     * - execute operation
     * - get the answers
     * - call the view.
     *
     * @return void
     * @throws Throwable
     */
    public function index() {

        try {

            $FLC = FLC::get_instance();
            // load the classes
            // Input data processor
            $this->input_data_processor = $this->class_loader('input_data_processor', [$FLC->request->request()]);
            // The output data holder
            $this->output_data = $this->class_loader('output_data');

            if ($this->is_logged_in()) {
                // create model and parse input data (inside)
                $this->model = $this->class_loader('model', [$FLC->db, $this->input_data_processor]);

                // get operation/ suboperation
                $operation = $this->input_data_processor->get_operation();
                $sub_operation = $this->input_data_processor->get_sub_operation();

                // check input validations
                if ($this->execute_validations($operation, $this->model->get_all_fields('rc'))) {

                    $answer = $this->execute_operation($operation, $sub_operation);

                    $this->output_data->set_success($answer->is_success());

                    if (!$answer->is_success()) {

                        $ex = $answer->get_exception();
                        if ($ex != null) {
                            $this->output_data->add_process_error($ex->getCode(), $ex->getMessage());
                        } else {
                            $this->output_data->set_answer_message($this->get_error_message($answer->get_return_code()), $answer->get_return_code());
                        }
                    } else {
                        $this->output_data->set_result_data($answer->get_result_array());
                    }
                }
            } else {
                $this->output_data->add_process_error(0, $this->_get_error_message('flc_common_not_logged_in'));
                $this->output_data->set_success(false);
            }

        } catch (Throwable $ex) {
            if (empty($this->output_data)) {
                $this->output_data = $this->class_loader('output_data');
            }
            $this->output_data->add_process_error($ex->getCode(), 'Unexpected errror', $ex);
            $this->output_data->set_success(false);

            flcCommon::log_message('error',$ex->getMessage());
        }

        $out_processor = $this->class_loader('output_data_processor');

        $data['answers'] = $out_processor->process_output_data($this->output_data);

        FLC::get_instance()->view($this->options['view'], $data);

    }

    // --------------------------------------------------------------------

    /**
     * Overridable to perform the operation / sub operation,
     *
     * @param string $p_operation
     * @param string $p_sub_operation
     *
     * @return flcPersistenceAccessorAnswer
     */
    abstract protected function execute_operation(string $p_operation, string $p_sub_operation): flcPersistenceAccessorAnswer;



    // --------------------------------------------------------------------

    /**
     * Helpers
     */

    /**
     * Helper method to get a readable error for some supported
     * transalated messages.
     *
     * @param int $p_error_code the error code
     *
     * @return string the translated message.
     *
     * @throws Throwable
     */
    protected function get_error_message(int $p_error_code): string {
        switch ($p_error_code) {
            case flcDbAccessor::$db_error_codes['DB_RECORD_MODIFIED'];
                $msg_id = 'flc_common_record_modified';
                break;
            case flcDbAccessor::$db_error_codes['DB_RECORD_NOT_EXIST'];
                $msg_id = 'flc_common_record_not_exist';
                break;
            case flcDbAccessor::$db_error_codes['DB_RECORD_EXIST'];
                $msg_id = 'flc_common_record_exist';
                break;
            case flcDbAccessor::$db_error_codes['DB_DUPLICATE_KEY'];
                $msg_id = 'flc_common_duplicate_key';
                break;
            case flcDbAccessor::$db_error_codes['DB_FOREIGN_KEY_ERROR'];
                $msg_id = 'flc_common_foreign_key';
                break;

            default:
                return "Unknown error code $p_error_code";
        }

        return $this->_get_error_message($msg_id);
    }


}