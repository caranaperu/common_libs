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
use flc\core\dto\flcInputDataProcessor;
use flc\core\dto\flcOutputData;
use flc\core\model\flcBaseModel;
use flc\core\FLC;
use flc\core\flcController;
use flc\core\flcValidation;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Throwable;

/**
 * Base controller , manage the default flow for all client hits.
 *
 */
abstract class flcBaseController extends flcController {

    protected flcInputDataProcessor $input_data_processor;
    protected flcOutputData         $output_data;

    protected flcBaseModel $model;

    protected array $options = [];

    public function __construct() {
        @set_exception_handler([
            $this,
            'controller_exception_handler',
        ]);

    }


    /**
     *
     * This is the requirements of the options for this base controller, we need to define
     * - Input data processor (namespace,class,path) ,  instance of flcInputDataProcessor
     * - Output data  (namespace,class,path) , instance of flcOutputData
     * - Output data processor  (namespace,class,path) , instance of flcOutputDataProcessor
     * - model  (namespace,class,path)
     * - view  (view class name on the views directory of the application)
     * - language file  (the language file , basically for field names and validations for example, need to be on the
     *   application language directory)
     *
     * For example :
     *
     *      <PRE>
     *      $this->options = [
     *         'input_data_processor' => [
     *             'namespace' => 'flc\app\input',
     *             'class' => 'SmartclientJsonInputDataProcessor',
     *             'path' => BASEPATH.'/app/input'
     *         ],
     *         'output_data' => [
     *             'namespace' => 'flc\core\dto',
     *             'class' => 'flcOutputData',
     *             'path' => BASEPATH.'/core/dto'
     *         ],
     *         'output_data_processor' => [
     *             'namespace' => 'flc\app\output',
     *             'class' => 'SmartclientJsonOutputDataProcessor',
     *             'path' => BASEPATH.'/app/output'
     *         ],
     *         'model' => [
     *             'namespace' => 'flc\tests\apptests\backend\model',
     *             'class' => 'paises_model',
     *             'path' => BASEPATH.'/tests/apptests/backend/model'
     *         ],
     *         'view' => 'paises_view',
     *         'language_file' => 'paises'
     *     ];
     * </pre>
     *
     *
     * @return void.
     */
    abstract protected function init_options(): void;

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
     * The default main entry .
     * - check if a user is logged in (depends on a property)
     * - do validations
     * - execute operation
     * - get the answers
     * - call the view.
     *
     * @return void
     * @throws Exception
     */
    public function index() {
        try {

            // load the classes
            // Input data processor
            $this->input_data_processor = $this->class_loader('input_data_processor', [$_REQUEST]);
            // The output data holder
            $this->output_data = $this->class_loader('output_data');

            if ($this->is_logged_in()) {
                // create model and parse input data (inside)
                $this->model = $this->class_loader('model', [FLC::get_instance()->db, $this->input_data_processor]);

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
                            $this->output_data->add_process_error($ex->getCode(), $ex->getMessage(), $ex);
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
     * helper method to load dinamycally the classese defined on the options array ,
     * input data processor , output data , output data processor and the model.
     *
     * @param string $p_key
     * @param array  $p_class_args
     *
     * @return mixed|object|string|null
     */
    protected function class_loader(string $p_key, array $p_class_args = []) {
        if ($this->options && count($this->options)) {
            if (isset($this->options[$p_key])) {
                $namespace = $this->options[$p_key]['namespace'] ?? '';
                $class = $this->options[$p_key]['class'] ?? '';
                $path = $this->options[$p_key]['path'] ?? '/';

                if ($p_key == 'input_data_processor') {
                    $base_class = 'flc\core\dto\flcInputDataProcessor';
                    $msg = 'input data processor';

                } elseif ($p_key == 'output_data') {
                    $base_class = 'flc\core\dto\flcOutputData';
                    $msg = 'output data';

                } elseif ($p_key == 'output_data_processor') {
                    $base_class = 'flc\core\dto\flcOutputDataProcessor';
                    $msg = 'output data processor';

                } else {
                    // model
                    $base_class = 'flc\core\model\flcBaseModel';
                    $msg = 'model';
                }

                // full class including namespace
                $ns_class = "$namespace\\$class";

                $file_path = "$path/$class.php";
                if (!file_exists($file_path)) {
                    throw new RuntimeException("The $msg [ $class ] doesnt exist, terminal error - BC00001");
                }

                include_once $file_path;

                // Class verification
                if (!class_exists($ns_class, false)) {
                    throw new RuntimeException("Cant load the $msg [ $ns_class ], terminal error - BC00002");
                }

                try {

                    $reflection = new ReflectionClass($ns_class);

                    if ($base_class != $ns_class && !$reflection->isSubclassOf($base_class)) {
                        throw new RuntimeException("$msg [ $ns_class ] need to be an instance of $base_class - BC00003");
                    }

                    if (count($p_class_args) > 0) {
                        return $reflection->newInstanceArgs($p_class_args);
                    }

                    return new $ns_class;

                } catch (ReflectionException $ex) {
                    // why can be enter here?
                    throw new RuntimeException("$msg [ $ns_class ] doesnt exist - BC00004");

                }

            } else {
                throw new RuntimeException("No $p_key options defined, terminal error - BC00005");
            }
        } else {
            throw new RuntimeException("No controller options defined, terminal error - BC00006");
        }
    }

    // --------------------------------------------------------------------

    /**
     * Funcion callback for not controlled exceptions
     *
     * @param Throwable $ex
     */
    public function controller_exception_handler(Throwable $ex) {
        // just in case is not already loaded , because a prematre exception
        $this->init_options();

        $this->output_data = $this->class_loader('output_data');
        $this->output_data->add_process_error($ex->getCode(), 'Unexpected errror', $ex);
        $this->output_data->set_success(false);

        $out_processor = $this->class_loader('output_data_processor');

        echo $out_processor->process_output_data($this->output_data);

        if (FLC::get_instance()->db) {
            FLC::get_instance()->db->close();
        }
    }

    // --------------------------------------------------------------------


    /**
     * @inheritdoc
     */
    public function is_logged_in(): bool {
        // default implementation use session
        if ($this->check_logged_in) {
            if (FLC::get_instance()->session()->has($this->logged_in_key)) {
                return FLC::get_instance()->session()->get($this->logged_in_key) ?? false;
            } else {
                return false;
            }
        } else {
            // fake login
            return true;
        }
    }

    // --------------------------------------------------------------------

    /**
     * @inheritDoc
     */
    public function set_logged_in(bool $p_is_loggedd_in): void {
        // default implementation
        FLC::get_instance()->session()->set($this->logged_in_key, $p_is_loggedd_in);
    }

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
     * @throws Exception
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

    /**
     * Low level to get the translated message
     *
     * @param string $msg_id the message id in the translation file
     *
     * @return string with the translation message.
     * @throws Exception
     */
    private function _get_error_message(string $msg_id): string {
        $flc = FLC::get_instance();
        if (!$flc->lang->load('flc_common')) {
            throw new RuntimeException('Invalid language file flc_common (check deploy of library)- BC00008');
        }

        return $flc->lang->line($msg_id);

    }
}