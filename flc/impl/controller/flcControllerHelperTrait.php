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

namespace flc\impl\controller;

use Exception;
use flc\core\dto\flcInputDataProcessor;
use flc\core\dto\flcOutputData;
use flc\core\FLC;
use flc\flcCommon;
use ReflectionClass;
use ReflectionException;
use RuntimeException;
use Throwable;

/**
 * Trait with a common functions to implement specific controllers
 */
trait flcControllerHelperTrait {
    protected flcInputDataProcessor $input_data_processor;
    protected flcOutputData         $output_data;
    protected array $options = [];


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
     * Also can be added other options required by specific implementations of this class.
     *
     *
     * @return void.
     */
    abstract protected function init_options(): void;

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
     * Funcion callback for not controlled exceptions,
     * Need to be public !!!!!
     *
     * @param Throwable $ex
     *
     * @throws Exception
     */
    public function controller_exception_handler(Throwable $ex) {
        // just in case is not already loaded , because a prematre exception
        $this->init_options();

        $this->output_data = $this->class_loader('output_data');
        $this->output_data->add_process_error($ex->getCode(), 'Unexpected errror', $ex);
        $this->output_data->set_success(false);

        $out_processor = $this->class_loader('output_data_processor');

        echo $out_processor->process_output_data($this->output_data);
        flcCommon::log_message('error',$ex->getMessage());
    }

    // --------------------------------------------------------------------

    /**
     * Low level to get the translated message
     *
     * @param string $msg_id the message id in the translation file
     *
     * @return string with the translation message.
     * @throws Throwable
     */
    protected function _get_error_message(string $msg_id): string {
        $flc = FLC::get_instance();
        if (!$flc->lang->load('flc_common')) {
            throw new RuntimeException('Invalid language file flc_common (check deploy of library)- BC00008');
        }

        return $flc->lang->line($msg_id);

    }
}


