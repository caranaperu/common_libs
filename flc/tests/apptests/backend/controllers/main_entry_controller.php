<?php

namespace flc\tests\apptests\backend\controllers;

use flc\core\FLC;
use flc\core\flcController;

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class main_entry_controller extends flcController {


    public function index() {
        FLC::get_instance()->view('mainEntryView', []);
    }

    public function is_logged_in(): bool {
        return true;
    }

    public function set_logged_in(bool $p_is_loggedd_in): void {
        // TODO: Implement set_logged_in() method.
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */