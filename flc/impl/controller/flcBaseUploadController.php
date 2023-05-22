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

use flc\core\FLC;
use flc\core\flcController;
use flc\core\flcFileUploader;
use Throwable;

/**
 * Base controller , manage the default flow for all client hits.
 *
 */
abstract class flcBaseUploadController extends flcController {
    use flcControllerHelperTrait;

    /**
     * After the file is uploaded , under this key in the ouput data
     * will be putted the final uploaded file name (remember encryption)
     *
     * @var string by default 'flc_file_uploaded'
     */
    protected string $output_data_key = 'flc_file_uploaded';

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
        // this init option need to be the required by flcFileUploader
        // @see flcFileUploader
        // Also need the standard options required by flcControllerHelperTrait
        // @see flcControllerHelperTrait::init_options
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
     *
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


            $this->output_data->set_success(false);
            if ($this->is_logged_in()) {

                $this->pre_file_upload();

                $uploader = new flcFileUploader($this->options);
                if ($uploader->do_upload()) {
                    $this->output_data->set_success(true);
                    $this->output_data->set_result_data([$this->output_data_key => $this->get_uploaded_filepath($uploader->get_uploaded_filename())]);

                } else {
                    $this->output_data->set_answer_message($this->_get_error_message($uploader->get_error_code()), 0);
                }

            } else {
                $this->output_data->add_process_error(0, $this->_get_error_message('flc_common_not_logged_in'));
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

    /**
     * Returns the uploaded file path relative to the web site , this is because if the html
     * client require a link to the image this cant be an absolute path.
     *
     * Remember that the repository of uploaded files can be in any place of the server.
     *
     * Overload this method is required.
     *
     * @param string $p_uploaded_filename the uploaded filename (encrypted or not depending)
     *
     * @return string with the websetite relative path.
     */
    protected function get_uploaded_filepath(string $p_uploaded_filename): string {
        // If the upload path ends with the directory separator?
        if (substr($this->options['upload_path'], -strlen(DIRECTORY_SEPARATOR)) === DIRECTORY_SEPARATOR) {
            return $this->options['upload_path'].$p_uploaded_filename;

        } else {
            // otherwise
            return $this->options['upload_path'].DIRECTORY_SEPARATOR.$p_uploaded_filename;

        }
    }

    /**
     * Called after input processor but befor upload a file , good moment
     * for setup for example some options that depends on input data and can be
     * initialized before this moment.
     * @return void
     */
    protected function pre_file_upload(): void {
        // override , the default do nothing
    }

}