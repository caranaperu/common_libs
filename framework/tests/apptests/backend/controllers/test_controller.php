<?php

namespace framework\tests\controllers;

use framework\core\dto\flcInputData;
use framework\core\FLC;
use framework\core\flcController;
use framework\tests\input\TestInputData;


class test_controller extends flcController {

    public function index() {
        // parse input data
        $input_data = new TestInputData($_REQUEST);
        $model = new \atletas_model(FLC::get_instance()->db,$input_data);

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
}