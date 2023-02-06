<?php

namespace flc\tests\controllers;


use flc\core\flcController;

class flcControllerExt extends flcController {

    public function index() {
        // TODO: Implement index() method.
        echo 'flcControllerExt-index'.PHP_EOL;
    }

    public function is_logged_in(): bool {
       return true;
    }

    public function set_logged_in(bool $p_is_loggedd_in): void {
        // TODO: Implement set_logged_in() method.
    }
}