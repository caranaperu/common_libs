<?php

namespace framework\core;

use framework\database\driver\flcDriver;
use framework\flcCommon;

class FLC {
    public array $config;

    /**
     * The default db driver instance for the framework
     *
     * @var flcDriver
     */
    public flcDriver $DB;

    public flcLanguage $lang;

    public flcValidation $validation;

    private array $_vrules = [];

    public function set_validations(string $p_filename,bool $p_reset = true) : bool {
        if ($p_reset) {
            $this->_vrules = [];
        }

        $validations = flcCommon::load_validation_config($p_filename);
        if (isset($validations)) {
            if (!$p_reset) {
                if (count($validations) == 1) {
                    $this->_vrules = array_merge($this->_vrules,$validations);
                } else {
                    flcCommon::log_message('warning','Append validations only allow a validation group ');
                    return true;
                }
            } else {

                $this->_vrules = $validations;
            }
        }
        return true;
    }

    public function get_rules(string $p_group = ''): array {
        if (count($this->_vrules) > 0) {
            return isset($this->_vrules[$p_group]) ? $this->_vrules[$p_group] : $this->_vrules;
        }

        return [];
    }

    public function &get_controller() {
        // corregir luego.
        return null;
    }


}