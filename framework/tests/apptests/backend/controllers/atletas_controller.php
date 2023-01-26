<?php

namespace framework\tests\apptests\backend\controllers;

require_once '/var/www/common/framework/tests/apptests/backend/model/atletas_model.php';


use framework\impl\controller\flcCRUDController;


class atletas_controller extends flcCRUDController {

    protected function init_options() : void {
        $this->options = [
            'input_data_processor' => [
                'namespace' => 'framework\app\input',
                'class' => 'SmartclientJsonInputDataProcessor',
                'path' => BASEPATH.'/app/input'
            ],
            'output_data' => [
                'namespace' => 'framework\core\dto',
                'class' => 'flcOutputData',
                'path' => BASEPATH.'/core/dto'
            ],
            'output_data_processor' => [
                'namespace' => 'framework\app\output',
                'class' => 'SmartclientJsonOutputDataProcessor',
                'path' => BASEPATH.'/app/output'

            ],
            'model' => [
                'namespace' => 'framework\tests\apptests\backend\model',
                'class' => 'atletas_model',
                'path' => BASEPATH.'/tests/apptests/backend/model'
            ],
            'view' => 'atletas_view',
            'language_file' => 'atletas',
            'validation' => [
                'add' => [
                    'file' => 'atletas_validation',
                    'group' => 'v_atletas',
                    'rules' => 'addAtletas'
                ],
                'upd' => [
                    'file' => 'atletas_validation',
                    'group' => 'v_atletas',
                    'rules' => 'updAtletas'
                ],
                'read' => [
                    'file' => 'atletas_validation',
                    'group' => 'v_atletas',
                    'rules' => 'getAtletas'
                ],
                'del' => [
                    'file' => 'atletas_validation',
                    'group' => 'v_atletas',
                    'rules' => 'delAtletas'
                ]
            ]
        ];
    }
}