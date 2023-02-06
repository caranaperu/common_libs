<?php

namespace flc\tests\apptests\backend\controllers;

use flc\app\input\SmartclientJsonInputDataProcessor;
use flc\core\FLC;
use flc\core\flcController;
use flc\tests\apptests\backend\model\atletas_model;


class test_controller extends flcController {

    public function index() {
        // parse input data
        $input_data = new SmartclientJsonInputDataProcessor($_REQUEST);
        $model = new atletas_model(FLC::get_instance()->db,$input_data);

        $operation = $input_data->get_operation();
        $sub_operation = $input_data->get_sub_operation();

        if ($operation == 'fetch') {
            $result = $model->fetch();

        }

        $data = $result;


        // Test session
/*        $session = FLC::get_instance()->session();

        if ($session->has('test')) {
            $session->test = 'Soy un test 2';

        } else {
            $session->test = 'Soy un test';

        }*/

        FLC::get_instance()->view('atletas_view',$data);

    }

    public function is_logged_in(): bool {
       return true;
    }

    public function set_logged_in(bool $p_is_loggedd_in): void {
        // TODO: Implement set_logged_in() method.
    }
}