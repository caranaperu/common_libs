<?php

namespace framework\tests\controllers;

use framework\core\FLC;
use framework\core\flcController;


class my_controller extends flcController {

    public function index() {
        //parent::index();
        // TODO: Implement index() method.
        echo 'PASO INDEX'.PHP_EOL;

        //$res = ['var1'=>1,'var2'=>3];
        $res = new \stdClass();
        $res->x = 100;
        $res->y = 200;

        $data['x'] = 300;
        $data['y'] = 400;

        $data2 = ['x'=>1000,'y'=>2000];

        FLC::get_instance()->view('my_testview',$data2);
        FLC::get_instance()->view('my_testview_2',$data2);


    }
}