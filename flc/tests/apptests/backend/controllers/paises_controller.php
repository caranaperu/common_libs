<?php

namespace flc\tests\apptests\backend\controllers;

require_once '/var/www/common/flc/tests/apptests/backend/model/paises_model.php';

use flc\impl\controller\flcCRUDController;


class paises_controller extends flcCRUDController {

    protected function init_options() : void {
        $this->options = [
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
            'model' => [
                'namespace' => 'flc\tests\apptests\backend\model',
                'class' => 'paises_model',
                'path' => BASEPATH.'/tests/apptests/backend/model'
            ],
            'view' => 'paises_view',
            'language_file' => 'paises'
        ];
    }
}