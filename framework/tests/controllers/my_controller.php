<?php

namespace framework\core\accessor\core\model\tests\controllers;

use framework\core\accessor\core\model\core\FLC;
use framework\core\accessor\core\model\core\flcController;


class my_controller extends flcControllerExt {

    public function index() {
        //parent::index();
        // TODO: Implement index() method.
        //echo 'PASO INDEX'.PHP_EOL;

        //$res = ['var1'=>1,'var2'=>3];
        $res = new \stdClass();
        $res->x = 100;
        $res->y = 200;

        $data['x'] = 300;
        $data['y'] = 400;

        $data2 = ['x'=>1000,'y'=>2000];

        // Test session
        $session = FLC::get_instance()->session();

        if ($session->has('test')) {
            $session->test = 'Soy un test 2';

        } else {
            $session->test = 'Soy un test';

        }

        FLC::get_instance()->view('my_testview',$data2);
        FLC::get_instance()->view('my_testview_2',$data2);


    }
}