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
    private array       $_data;
    private array       $_field_data;
    private array       $_error_messages = [];
    private             $_controller;
    private flcLanguage $_lang;

    public function __construct(flcLanguage $p_lang, $p_controller = null) {
        if (isset($p_controller)) {
            $this->_controller = $p_controller;
        }

        $this->_lang = $p_lang;
    }


    /**
     *
     * set the data that contains the fields and values to check.
     * If this run in a www context this values are usually in the
     * $_POST array.
     *
     * @param array $p_data with the validation data
     *
     * @return void
     */
    public function set_data(array $p_data) {
        if (count($p_data) > 0) {
            $this->_data = $p_data;
        }

    }

    // --------------------------------------------------------------------

    /**
     * Set Error Message
     *
     * Lets users set their own error messages on the fly. Note:
     * The key name has to match the function name that it corresponds.
     *
     * $lang can be a string , in that case value need to be defined.
     * if is an array of error messages for multiple validations value parameter
     * is useless.
     *
     *
     * @param array | string
     * @param string
     *
     */
    public function set_message($lang, string $val = '') {
        if (!is_array($lang)) {
            $lang = [$lang => $val];
        }

        // add to the custom error messages array
        $this->_error_messages = array_merge($this->_error_messages, $lang);
    }

    // --------------------------------------------------------------------

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

        // print_r($this->_field_data);

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
        $this->_lang->load('default_validations');

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

        return true;

    }

    protected function _execute($row, string $p_postdata): void {
        $result = false;
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
                        // for cases like :
                        // add_rule('message2', 'Message2', [['rule_id',[$users_model, 'valid_username']]]);
                        $callable = true; // the class name
                        $callable_msg = $rule[0];
                        $rule = $rule[1]; // // the validator function name
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

                            $result = false;
                        } else {
                            // execute a callback
                            $result = $this->_controller->$rule($p_postdata);
                        }

                    }
                } else {
                    // execute a callable or callback
                    $result = is_array($rule) ? call_user_func($rule, $p_postdata) : $rule($p_postdata);

                }


            } elseif (!method_exists($this, $rule)) {
                // if not a method in this class we try in the standard library
                if (function_exists($rule)) {
                    $result = ($param !== false) ? $rule($p_postdata, $param) : $rule($p_postdata);
                } else {
                    flcCommon::log_message('info', "Validation function $rule doesnt exist");
                    $result = false;
                }
            } else {
                $result = $this->$rule($p_postdata, $param);
            }

            // error management
            if ($result === false) {

                // Check if callable hat a rule name to check an specific error message.
                if ($callable === true) {
                    //echo 'paso callable message'.PHP_EOL;
                    if (isset($callable_msg)) {
                        // for cases like :
                        // add_rule('message2', 'Message2', [['rule_id',[$users_model, 'valid_username']]]);
                        $line = $this->_get_error_message($callable_msg, $row['field']);

                    } else {
                        $line = $this->_lang->line('validation_error_message_not_set').'(Anonymous function)';

                    }

                } else {

                    $line = $this->_get_error_message($rule, $row['field']);
                }

                // Is the parameter we are inserting into the error message is the name
                // of another field? If so we need to grab its "field label"
                if (isset($this->_field_data[$param], $this->_field_data[$param]['label'])) {
                    $param = $this->_translate_fieldname($this->_field_data[$param]['label']);
                }

                // now we obtain the final message.
                $message = $this->_build_error_msg($line, $this->_translate_fieldname($row['label']), $param);

                echo '**** Message ============>'.$message.PHP_EOL;

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

    // --------------------------------------------------------------------

    /**
     * Get the error message for the rule
     *
     * @param string $rule The rule name
     * @param string $field The field name
     *
     * @return    string
     */
    protected function _get_error_message($rule, string $field): string {
        // check if a custom message is defined through validation config row.

        if (isset($this->_field_data[$field]['errors'][$rule])) {
            return $this->_field_data[$field]['errors'][$rule];
        } // check if a custom message has been set using the set_message() function
        elseif (isset($this->_error_messages[$rule])) {
            return $this->_error_messages[$rule];
        } else {
            // callable ? only if it is an array
            if (is_array($rule)) {
                if (false !== ($line = $this->_lang->line('validation_'.$rule[0]))) {
                    return $line;
                }
            } elseif (false !== ($line = $this->_lang->line('validation_'.$rule))) {
                return $line;
            } // DEPRECATED support for non-prefixed keys, lang file again
            elseif (false !== ($line = $this->_lang->line($rule, false))) {
                return $line;
            }
        }

        return $this->_lang->line('validation_error_message_not_set').'('.$rule.')';
    }

    // --------------------------------------------------------------------

    /**
     * Translate a field name
     *
     * @param string    the field name
     *
     * @return    string
     */
    protected function _translate_fieldname(string $p_fieldname): string {
        // Do we need to translate the field name? We look for the prefix 'lang:' to determine this
        // If we find one, but there's no translation for the string - just return it
        if (sscanf($p_fieldname, 'lang:%s', $line) === 1 && false === ($p_fieldname = $this->lang->line($line, false))) {
            return $line;
        }

        return $p_fieldname;
    }

    // --------------------------------------------------------------------

    /**
     * Build an error message using the field and param.
     *
     * @param string $line The error message line
     * @param string $field A field's human name
     * @param string $param A rule's optional parameter
     *
     * @return    string
     */
    protected function _build_error_msg(string $line, string $field = '', string $param = '') {
        // Check for %s in the string for legacy support.
        if (strpos($line, '%s') !== false) {
            return sprintf($line, $field, $param);
        }

        return str_replace(['{field}', '{param}'], [$field, $param], $line);
    }

    // --------------------------------------------------------------------

    /**
     * Reset validation vars
     *
     * Prevents subsequent validation routines from being affected by the
     * results of any previous validation routine.
     *
     */
    public function reset_validation() {
        $this->_field_data = [];
        $this->_error_messages = [];

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
        return false;
        //return is_array($p_str) ? (empty($p_str) === false) : (trim($p_str) !== '');
    }

    // --------------------------------------------------------------------

    /**
     * Minimum Length
     *
     * @param string
     * @param string
     *
     * @return    bool
     */
    public function min_length(string $str, string $val): bool {
        if (!is_numeric($val)) {
            return false;
        }

        return ($val <= mb_strlen($str));
    }

    // --------------------------------------------------------------------

    /**
     * Max Length
     *
     * @param string
     * @param string
     *
     * @return    bool
     */
    public function max_length(string $str, string $val) {
        if (!is_numeric($val)) {
            return false;
        }

        return ($val >= mb_strlen($str));
    }

    // --------------------------------------------------------------------

    /**
     * Exact Length
     *
     * @param string
     * @param string
     *
     * @return    bool
     */
    public function exact_length(string $str, string $val): bool {
        if (!is_numeric($val)) {
            return false;
        }

        return (mb_strlen($str) === (int)$val);
    }

    // --------------------------------------------------------------------

    /**
     * Valid URL
     *
     * @param string $str
     *
     * @return    bool
     */
    public function valid_url(string $str): bool {
        if (empty($str)) {
            return false;
        } elseif (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', $str, $matches)) {
            if (empty($matches[2])) {
                return false;
            } elseif (!in_array(strtolower($matches[1]), ['http', 'https'], true)) {
                return false;
            }

            $str = $matches[2];
        }

        // PHP 7 accepts IPv6 addresses within square brackets as hostnames,
        // but it appears that the PR that came in with https://bugs.php.net/bug.php?id=68039
        // was never merged into a PHP 5 branch ... https://3v4l.org/8PsSN
        if (preg_match('/^\[([^\]]+)\]/', $str, $matches) && !is_php('7') && filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            $str = 'ipv6.host'.substr($str, strlen($matches[1]) + 2);
        }

        return (filter_var('http://'.$str, FILTER_VALIDATE_URL) !== false);
    }

    // --------------------------------------------------------------------

    /**
     * Valid Email
     *
     * @param string
     *
     * @return    bool
     */
    public function valid_email(string $str): bool {
        if (function_exists('idn_to_ascii') && sscanf($str, '%[^@]@%s', $name, $domain) === 2) {
            $str = $name.'@'.idn_to_ascii($domain);
        }

        return (bool)filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    // --------------------------------------------------------------------

    /**
     * Valid Emails
     *
     * @param string
     *
     * @return    bool
     */
    public function valid_emails(string $str): bool {
        if (strpos($str, ',') === false) {
            return $this->valid_email(trim($str));
        }

        foreach (explode(',', $str) as $email) {
            if (trim($email) !== '' && $this->valid_email(trim($email)) === false) {
                return false;
            }
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Alpha
     *
     * @param string
     *
     * @return    bool
     */
    public function alpha(string $str): bool {
        return ctype_alpha($str);
    }

    // --------------------------------------------------------------------

    /**
     * Alpha-numeric
     *
     * @param string
     *
     * @return    bool
     */
    public function alpha_numeric(string $str): bool {
        return ctype_alnum((string)$str);
    }

    // --------------------------------------------------------------------

    /**
     * Alpha-numeric w/ spaces
     *
     * @param string
     *
     * @return    bool
     */
    public function alpha_numeric_spaces(string $str): bool {
        return (bool)preg_match('/^[A-Z0-9 ]+$/i', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Alpha-numeric with underscores and dashes
     *
     * @param string
     *
     * @return    bool
     */
    public function alpha_dash(string $str): bool {
        return (bool)preg_match('/^[a-z0-9_-]+$/i', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Numeric
     *
     * @param string
     *
     * @return    bool
     */
    public function numeric(string $str): bool {
        return (bool)preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $str);

    }

    // --------------------------------------------------------------------

    /**
     * Integer
     *
     * @param string
     *
     * @return    bool
     */
    public function integer(string $str): bool {
        return (bool)preg_match('/^[\-+]?[0-9]+$/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Decimal number
     *
     * @param string
     *
     * @return    bool
     */
    public function decimal(string $str): bool {
        return (bool)preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $str);
    }

    // --------------------------------------------------------------------

    /**
     * Greater than
     *
     * @param string
     * @param int
     *
     * @return    bool
     */
    public function greater_than(string $str, string $min): bool {
        return is_numeric($str) ? ($str > $min) : false;
    }

    // --------------------------------------------------------------------

    /**
     * Equal to or Greater than
     *
     * @param string
     * @param int
     *
     * @return    bool
     */
    public function greater_than_equal_to(string $str, string $min): bool {
        return is_numeric($str) ? ($str >= $min) : false;
    }

    // --------------------------------------------------------------------

    /**
     * Less than
     *
     * @param string
     * @param int
     *
     * @return    bool
     */
    public function less_than(string $str, string $max): bool {
        return is_numeric($str) ? ($str < $max) : false;
    }

    // --------------------------------------------------------------------

    /**
     * Equal to or Less than
     *
     * @param string
     * @param int
     *
     * @return    bool
     */
    public function less_than_equal_to(string $str, string $max): bool {
        return is_numeric($str) ? ($str <= $max) : false;
    }

    // --------------------------------------------------------------------

    /**
     * Value should be within an array of values
     *
     * @param string
     * @param string
     *
     * @return    bool
     */
    public function in_list(string $value, array $list): bool {
        return in_array($value, explode(',', $list), true);
    }

    // --------------------------------------------------------------------

    /**
     * Is a Natural number  (0,1,2,3, etc.)
     *
     * @param string
     *
     * @return    bool
     */
    public function is_natural(string $str): bool {
        return ctype_digit((string)$str);
    }

    // --------------------------------------------------------------------

    /**
     * Is a Natural number, but not a zero  (1,2,3, etc.)
     *
     * @param string
     *
     * @return    bool
     */
    public function is_natural_no_zero(string $str): bool {
        return ($str != 0 && ctype_digit((string)$str));
    }

    // --------------------------------------------------------------------

    /**
     * Valid Base64
     *
     * Tests a string for characters outside of the Base64 alphabet
     * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
     *
     * @param string
     *
     * @return    bool
     */
    public function valid_base64(string $str): bool {
        return (base64_encode(base64_decode($str)) === $str);
    }

}

