<?php

namespace framework\tests\apptests\backend\controllers;

use framework\core\FLC;
use framework\core\flcController;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class main_entry_controller extends flcController {


    public function index() {
        FLC::get_instance()->view('mainEntryView',[]);
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */