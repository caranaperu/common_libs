<?php

namespace framework\tests\controllers;

use framework\core\flcController;
use framework\core\flcServiceLocator;


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

        $view = flcServiceLocator::get_instance()->service('views','my_testview',$data2);
    }
}