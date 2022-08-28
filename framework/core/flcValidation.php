<?php


namespace framework\core;

use framework\flcCommon;

/**
 * FLabsCode
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2022 - 2022, Future Labs Corp-
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    FLabsCode
 * @author    Carlos Arana
 * @copyright    Copyright (c) 2022 - 2022, FLabsCode
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://flabscorpprods.com
 * @since    Version 1.0.0
 * @filesource
 */
class flcValidation {

    /**
     * Repository of all the rules loaded.
     * @var array
     */
    private array $_data;
    private array $_field_data;
    private       $_controller;

    public function __construct(&$p_controller = null) {
        if (isset($p_controller)) {
            $this->_controller = $p_controller;
        }
    }


    public function set_data(array $p_data) {
        if (count($p_data) > 0) {
            $this->_data = $p_data;
        }

    }

    public function add_rule(string $p_field, string $p_label, $p_rules, ?array $p_errors = null) {

        if (empty($p_field) || empty($p_rules)) {
            // nothing to do
            return;
        }
        // If not label , use the field name
        $label = !empty($p_label) ? $p_label : $p_field;

        // if is an array correspond to something like this
        // array(
        //                'required',
        //                array($this->users_model, 'valid_username')
        //        )
        if (!is_array($p_rules)) {
            // rules pipe separated
            $rules = preg_split('/\|(?![^\[]*\])/', $p_rules);
        } else {
            $rules = $p_rules;
        }

        // Build our master array
        $this->_field_data[$p_field] = [
            'field' => $p_field,
            'label' => $label,
            'rules' => $rules,
            'errors' => $p_errors,
            'postdata' => null,
            'error' => ''
        ];

    }

    public function set_rules(array $p_rules) {
        //  claer current data
        $this->_field_data = [];

        foreach ($p_rules as $rule) {
            // if not a well format rule , skip.
            if (!isset($rule['field']) || !isset($rule['rules'])) {
                continue;
            }

            // If not label , use the field name
            $label = $rule['label'] ?? $rule['field'];

            // Add the custom error message array
            $errors = (isset($rule['errors']) && is_array($rule['errors'])) ? $rule['errors'] : [];

            $this->add_rule($rule['field'], $label, $rule['rules'], $errors);

        }

        print_r($this->_field_data);

    }

    public function run(): bool {
        // no fields to verify
        if (isset($this->_data) && count($this->_data) == 0) {
            flcCommon::log_message('info', 'flcValitadion:run() - no data to validate');

            return false;
        }

        // no rules pre processed nothing to do.
        if (isset($this->_field_data) && count($this->_field_data) == 0) {
            flcCommon::log_message('info', 'flcValitadion:run() - no rules to validate');

            return false;

        }

        // load default messages for implemented validations
        $lang = new flcLanguage();
        $lang->load('default_validations');

        // process the field by field validation
        foreach ($this->_field_data as $field => $row) {
            // Don't try to validate if we have no rules set
            if (empty($row['rules'])) {
                continue;
            }

            // if the data to validate doesnt exist on the field list to validate
            // continue,
            if (!isset($this->_data[$row['field']])) {
                continue;
            }

            $this->_execute($row, $this->_data[$row['field']]);
        }

        unset($lang);

        return true;

    }

    protected function _execute($row, $p_postdata) {
        $rules = $row['rules'];

        $rules = $this->_order_rules($rules);

        // Is the rule a callback or a cllable?
        foreach ($rules as $rule) {
            $callback = $callable = false;

            if (is_string($rule)) {
                if (strpos($rule, 'callback_') === 0) {
                    $rule = substr($rule, 9);
                    $callback = true;
                }
            } elseif (is_callable($rule)) {
                // Function in this class
                $callable = true;
            } elseif (is_array($rule)) {
                if (isset($rule[0], $rule[1])) {
                    if (is_callable($rule[1])) {
                        // solving this case
                        //  array($this->users_model, 'valid_username')
                        $callable = true; // the class name
                        //  $rule = $rule[1]; // // the validator function name
                    } elseif (is_array($rule[1])) {
                        // solving this case
                        //  array('username_callable', array($this->users_model, 'valid_username'))
                        $field_name = $rule[0];
                        $callable = true; // the class that contains the function
                        $rule = $rule[1]; // the validator function name
                    }
                }
            }


            //
            // Strip the parameter (if exists) from the rule
            // Rules can contain a parameter: max_length[5]
            $param = false;
            if (!$callable && preg_match('/(.*?)\[(.*)\]/', $rule, $match)) {
                $rule = $match[1]; // rule name
                $param = $match[2]; // rule parameter
            }

            // Ignore empty, non-required inputs with a few exceptions ...
            if (!isset($p_postdata) or trim($p_postdata) === '' && $callback === false && $callable === false && !in_array($rule, [
                    'required',
                    'isset',
                    'matches'
                ], true)) {
                continue;
            }

            // if callback or standar function
            if ($callback || $callable !== false) {
                if ($callback) {
                    if (isset($this->_controller)) {
                        // if callback  search  on the controller , if exist call
                        if (!method_exists($this->_controller, $rule)) {
                            flcCommon::log_message('info', "unable to find callback method $rule");

                            return false;
                        } else {
                            // execute a callback
                            $result = $this->_controller->$rule($p_postdata);
                        }

                    }
                } else {
                    // execute a callable or callback
                    $result = is_array($rule) ? call_user_func($rule, $p_postdata) : $rule($p_postdata);

                    // Is $callable set to a rule name?
                    if ($callable !== false) {
                        $rule = $callable;
                    }
                }


            } elseif (!method_exists($this, $rule)) {
                // if not a method in this class we try in the standard library
                if (function_exists($rule)) {
                    $result = ($param !== false) ? $rule($p_postdata, $param) : $rule($p_postdata);
                } else {
                    flcCommon::log_message('info', "Validation function $rule doesnt exist");
                }
            } else {
                $result = $this->$rule($p_postdata, $param);
            }
        } // foreach ($rules as $rule)

    }

    /**
     * Prepare rules
     *
     * Re-orders the provided rules in order of importance, so that
     * they can easily be executed later without weird checks ...
     *
     * "Callbacks" are given the highest priority (always called),
     * followed by 'required' (called if callbacks didn't fail),
     * and then every next rule depends on the previous one passing.
     *
     * @param array $rules
     *
     * @return    array
     */
    protected function _order_rules(array $rules): array {
        $new_rules = [];
        $callbacks = [];

        foreach ($rules as $rule) {
            // Let 'required' always be the first (non-callback) rule
            if ($rule === 'required') {
                array_unshift($new_rules, 'required');
            } // 'isset' is a kind of a weird alias for 'required' ...
            elseif ($rule === 'isset' && (empty($new_rules) or $new_rules[0] !== 'required')) {
                array_unshift($new_rules, 'isset');
            } // The old/classic 'callback_'-prefixed rules
            elseif (is_string($rule) && strncmp('callback_', $rule, 9) === 0) {
                $callbacks[] = $rule;
            } // Proper callables
            elseif (is_callable($rule)) {
                $callbacks[] = $rule;
            } // "Named" callables; i.e. array('name' => $callable)
            elseif (is_array($rule) && isset($rule[0], $rule[1]) && is_callable($rule[1])) {
                $callbacks[] = $rule;
            } // Everything else goes at the end of the queue
            else {
                $new_rules[] = $rule;
            }
        }

        return array_merge($callbacks, $new_rules);
    }

    /**********************************************************************
     * Validation routines
     */

    // --------------------------------------------------------------------

    /**
     * Required
     *
     * @param string $p_str with the value to check
     *
     * @return    bool true if the rule is ok otherwise false.
     */
    public function required(string $p_str): bool {
        return is_array($p_str) ? (empty($p_str) === false) : (trim($p_str) !== '');
    }

}

