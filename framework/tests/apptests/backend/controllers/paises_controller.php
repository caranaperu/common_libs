<?php

namespace framework\tests\apptests\backend\controllers;

require_once '/var/www/common/framework/tests/apptests/backend/model/paises_model.php';
require_once '/var/www/common/framework/tests/apptests/backend/input/TestInputData.php';
require_once '/var/www/common/framework/tests/apptests/backend/output/TestOutputData.php';

use framework\core\dto\flcOutputData;
use framework\core\FLC;
use framework\core\flcController;
use framework\tests\apptests\backend\model\paises_model;
use framework\tests\apptests\backend\input\TestInputData;
use framework\tests\apptests\backend\output\TestOutputData;


class paises_controller extends flcController {

    public function index() {
        // parse input data
        $input_data = new TestInputData($_REQUEST);
        $model = new paises_model(FLC::get_instance()->db,$input_data);

        // prepare otuput data
        $output_data = new flcOutputData();
        $output_data->set_start_row($input_data->get_start_row());
        $output_data->set_end_row($input_data->get_end_row());

        $operation = $input_data->get_operation();
        $sub_operation = $input_data->get_sub_operation();

        $result = [];
        if ($operation == 'fetch') {
            $result = $model->fetch();

        }

        $output_data->set_result_data($result);

        $t = new TestOutputData();

        $data['answers'] = $t->process($output_data);


        // Test session
/*        $session = FLC::get_instance()->session();

        if ($session->has('test')) {
            $session->test = 'Soy un test 2';

        } else {
            $session->test = 'Soy un test';

        }*/

        FLC::get_instance()->view('paises_view',$data);

    }
}