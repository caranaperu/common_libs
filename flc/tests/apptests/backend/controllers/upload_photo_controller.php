<?php

namespace flc\tests\apptests\backend\controllers;

use flc\impl\controller\flcBaseUploadController;


class upload_photo_controller extends flcBaseUploadController {

    protected function init_options(): void {
        $this->options = [
            // Standard controller options
            'input_data_processor' => [
                'namespace' => 'flc\app\input',
                'class' => 'SmartclientJsonInputDataProcessor',
                'path' => BASEPATH.'/app/input'
            ],
            'output_data' => [
                'namespace' => 'flc\core\dto',
                'class' => 'flcOutputData',
                'path' => BASEPATH.'/core/dto'
            ],
            'output_data_processor' => [
                'namespace' => 'flc\app\output',
                'class' => 'SmartclientJsonOutputDataProcessor',
                'path' => BASEPATH.'/app/output'

            ],
            'view' => 'common_view',
            //'language_file' => 'paises',

            // file uploader options
            'field_name' => 'selectedImageFile',
            'mime_ext_allowed' => ['gif', 'jpg', 'jpeg','png'],
            'upload_path' => APPPATH . '../photos/',
            'encrypt_file_name' => false,
            'max_file_size' => 4096 // 4mb
        ];

    }

    /**
     * @inheritdoc
     */
    protected function get_uploaded_filepath($p_uploaded_filename)  : string {
        return '../../photos/'.$p_uploaded_filename;
    }
}