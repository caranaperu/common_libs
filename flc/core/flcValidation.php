<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Inspired in codeigniter , all kudos for his authors
 *
 * @author Carlos Arana Reategui.
 *
 */

namespace flc\core;

use Exception;
use flc\flcCommon;
use flc\utils\flcStrUtils;
use Throwable;


/**
 * Validation class based on a set of rules associated to a fields.
 *
 * Important : This class is a modified one of Form_validation from codeigniter
 * all credits for his authors.
 */
class flcValidation {


    /**
     * Repository of the field data to validate fielname => value
     * @var array
     */
    private array $_data;

    /**
     * Repository of all the rules loaded.
     * @var array
     */
    private array $_field_data = [];

    /**
     * Array of validation errors
     *
     * @var array
     */
    private array $_error_array = [];

    /**
     * Array of custom error messages
     *
     * @var array
     */
    private array $_error_messages = [];

    /**
     * @var mixed a class instance that hold the csllback functions
     */
    private $_callback_class;

    /**
     * @var flcLanguage that contains the translation for the field names
     * and also will hold the validation messages.
     */
    private flcLanguage $_lang;

    /**
     * Constructor
     *
     * @param flcLanguage $p_lang that contains the translation for the field names
     * and also will hold the validation messages.
     *
     * @param object|null $p_callback_class a class instance that hold the callback functions
     * for validations with callbacks..
     */
    public function __construct(flcLanguage $p_lang, object $p_callback_class = null) {
        $this->set_callbacks_class($p_callback_class);
        $this->_lang = $p_lang;
    }

    /**
     * Set the class instance that hold the callback functions for validations.
     *
     * @param object|null $p_callback_class
     *
     * @return void
     */
    public function set_callbacks_class(?object $p_callback_class) {
        if ($p_callback_class !== null) {
            $this->_callback_class = $p_callback_class;
        }
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
     * $rule_id can be a string , in that case $rule_msg need to be defined.
     * if is an array of error messages for multiple validations, the  $rule_msg parameter
     * is useless.
     *
     *
     * @param array | string $p_rule_id a fielname or array of fielname => value
     * @param string         $p_rule_msg if rule_id is a rule name this will be the associated error message.
     *
     */
    public function set_message($p_rule_id, string $p_rule_msg = '') {
        if (!is_array($p_rule_id)) {
            $p_rule_id = [$p_rule_id => $p_rule_msg];
        }

        // add to the custom error messages array
        $this->_error_messages = array_merge($this->_error_messages, $p_rule_id);
    }

    // --------------------------------------------------------------------

    /**
     * Get Error Message
     *
     * Gets the error message associated with a particular field
     *
     * @param string $p_field Field name
     *
     * @return    string
     */
    public function error(string $p_field): string {
        if (empty($this->_field_data[$p_field]['error'])) {
            return '';
        }

        return $this->_field_data[$p_field]['error'];
    }


    // --------------------------------------------------------------------

    /**
     * Get Array of Error Messages
     *
     * Returns the error messages as an array
     *
     * @return    array
     */
    public function error_array(): array {
        return $this->_error_array;
    }

    // --------------------------------------------------------------------

    /**
     * Add a rule to the list of rules already exist.
     * Examples :
     * add_rule('field1', 'The field 1',['required','max_length[2]])
     * add_rule('field1', 'The field 1','required|max_length[2])
     *
     * // callable
     *      add_rule('username', 'The User Name', [['rule_id_for_username',[$users_model, 'valid_username']])
     *      add_rule('username', 'The User Name', [['rule_id_for_username',[$users_model,
     * 'valid_username']],,['rule_id_for_username' => 'EL %s no sirve'])
     *
     * // anonymous function
     *      add_rule('username', 'User Name', [['rule_id_for_username',function($str) {echo 'always false?';return
     * false;}]]); add_rule('username', 'User Name', [['rule_id_for_username',function($str) {echo 'always
     * false?';return false;}]],['rule_id_for_username' => 'Field %s is not valid']);
     *
     * // calbacks
     *      add_rule('fieldtest', 'Field Test', 'callback_fieldtest');
     *      add_rule('fieldtest', 'Field Test', 'callback_fieldtest', ['fieldtest' => 'You must provide with callback a
     * %s.']);
     *
     * @param string     $p_field the field name
     * @param string     $p_label the label to show in message for the field
     * @param mixed      $p_rules the rule or array of rules
     * @param array|null $p_errors the list of custom errors associated to a rule or rules
     *
     * @return void
     */
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
            'error' => ''
        ];

    }

    /**
     * Add a set of rules
     *
     * @param array $p_rules
     *
     * @return void
     */
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

    }

    /**
     * Validate each one of the fields on the data with field => values
     *
     * @return bool true if all validations are ok, false otherwise
     * @throws Exception
     */
    public function run(): bool {
        // no fields to verify
        if (isset($this->_data) && count($this->_data) == 0) {
            flcCommon::log_message('info', 'flcValitadion->run : no data to validate');

            return false;
        }

        // no rules pre processed nothing to do.
        if (isset($this->_field_data) && count($this->_field_data) == 0) {
            flcCommon::log_message('info', 'flcValitadion->run : no rules to validate');

            // if not rules to run , nothing to validte , all ok.
            return true;

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
            if (!array_key_exists($row['field'], $this->_data)) {
                continue;
            }

            $this->_execute($row, $this->_data[$row['field']]);
        }

        // Did we end up with any errors?
        $total_errors = count($this->_error_array);

        return ($total_errors === 0);

    }

    /**
     * Verify all the rules for a field
     * If one validation fails , put the errors on field data , Lstop and return
     *
     * @param mixed       $p_row the field data containing the rules for the fields
     * @param string|null $p_postdata the field value send to validate.
     *
     * @return void
     * @throws Throwable
     */
    protected function _execute($p_row, ?string $p_postdata): void {
        $result = false;
        $rules = $p_row['rules'];

        $rules = $this->_order_rules($rules);


        foreach ($rules as $rule) {
            $callback = $callable = false;

            if (is_string($rule)) {
                // is callback?
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
                        $callable_msg = $rule[0]; // the id of the message
                        $rule = $rule[1]; // // the validator function name
                    } elseif (is_array($rule[1])) {
                        // solving this case
                        //  array('username_callable', array($this->users_model, 'valid_username'))
                        // $field_name = $rule[0];
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
            if ((!isset($p_postdata) or trim($p_postdata) === '') && $callback === false && $callable === false && !in_array($rule, [
                    'required',
                    'isset',
                    'matches'
                ], true)) {
                continue;
            }

            // if callback or standar function
            if ($callback || $callable !== false) {
                if ($callback) {
                    if (isset($this->_callback_class)) {
                        // if callback  search  on the controller , if exist call
                        if (!method_exists($this->_callback_class, $rule)) {
                            flcCommon::log_message('info', "flcValidation->_execute : unable to find callback method $rule");

                            $result = false;
                        } else {
                            // execute a callback
                            $result = $this->_callback_class->$rule($p_postdata);
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
                    flcCommon::log_message('info', "flcValidation->_execute :Validation function $rule doesnt exist");
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
                        $line = $this->_get_error_message($callable_msg, $p_row['field']);

                    } else {
                        $line = $this->_lang->line('validation_error_message_not_set').'(Anonymous function)';

                    }

                } else {

                    $line = $this->_get_error_message($rule, $p_row['field']);
                }

                // Is the parameter we are inserting into the error message is the name
                // of another field? If so we need to grab its "field label"
                if (isset($this->_field_data[$param], $this->_field_data[$param]['label'])) {
                    $param = $this->_translate_fieldname($this->_field_data[$param]['label']);
                }

                // now we obtain the final message.
                $message = $this->_build_error_msg($line, $this->_translate_fieldname($p_row['label']), $param);

                // Save the error message
                $this->_field_data[$p_row['field']]['error'] = $message;

                if (!isset($this->_error_array[$p_row['field']])) {
                    $this->_error_array[$p_row['field']] = $message;
                }

                // one error per field is allowed , is one validation fails return
                return;

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
     * @param array $p_rules
     *
     * @return    array
     */
    protected function _order_rules(array $p_rules): array {
        $new_rules = [];
        $callbacks = [];

        foreach ($p_rules as $rule) {
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
     * @param string | array $p_rule The rule name ot array of rules
     * @param string         $p_field The field name
     *
     * @return    string
     */
    protected function _get_error_message($p_rule, string $p_field): string {
        // check if a custom message is defined through validation config row.

        if (isset($this->_field_data[$p_field]['errors'][$p_rule])) {
            return $this->_field_data[$p_field]['errors'][$p_rule];
        } // check if a custom message has been set using the set_message() function
        elseif (isset($this->_error_messages[$p_rule])) {
            return $this->_error_messages[$p_rule];
        } else {
            // callable ? only if it is an array
            $rulecheck = is_array($p_rule) ? $p_rule[0] : $p_rule;
            $line = $this->_lang->line('validation_'.$rulecheck);
            if (substr($line, 0, 18) !== "No translation for") {
                return $line;
            }
        }

        return $this->_lang->line('validation_error_message_not_set').'('.$p_rule.')';
    }

    // --------------------------------------------------------------------

    /**
     * Translate a field name
     *
     * @param string $p_fieldname the field name
     *
     * @return    string
     */
    protected function _translate_fieldname(string $p_fieldname): string {
        // Do we need to translate the field name? We look for the prefix 'lang:' to determine this
        // If we find one, but there's no translation for the string - just return it
        if (sscanf($p_fieldname, 'lang:%s', $line) === 1) {
            $p_fieldname = $this->_lang->line($line);
            if (substr($p_fieldname, 0, 18) == "No translation for") {
                return $line;
            }
        }

        return $p_fieldname;
    }

    // --------------------------------------------------------------------

    /**
     * Build an error message using the field and param.
     * IF the $line parameter contain %s the first one will be the field
     * and the second the value that cause the error.
     *
     * Never the use of  {field] and/or {param} can be mixed with %s type message
     * string.
     *
     *
     * @param string $p_line The error message line
     * @param string $p_field A field's human name
     * @param string $p_param A rule's optional parameter
     *
     * @return    string
     */
    protected function _build_error_msg(string $p_line, string $p_field = '', string $p_param = ''): string {
        // Check for %s in the string for legacy support.
        if (strpos($p_line, '%s') !== false) {
            return sprintf($p_line, $p_field, $p_param);
        }

        return str_replace(['{field}', '{param}'], [$p_field, $p_param], $p_line);
    }

    // --------------------------------------------------------------------

    /**
     * Reset validation vars
     *
     * Prevents subsequent validation routines from being affected by the
     * results of any previous validation routine.
     *
     */
    public function reset_validation(): void {
        $this->_field_data = [];
        $this->_error_messages = [];
        $this->_error_array = [];


    }


    /**********************************************************************
     * Validation routines
     */


    // --------------------------------------------------------------------

    /**
     * Required
     *
     * @param string|null $p_str with the value to check
     *
     * @return    bool true if the rule is ok otherwise false.
     */
    public function required(?string $p_str): bool {
        return ($p_str != null && trim($p_str) !== '');
    }

    // --------------------------------------------------------------------

    /**
     * Performs a Regular Expression match test.
     *
     * @param string $p_str value to check
     * @param string $p_regex the regular expresion
     *
     * @return    bool
     */
    public function regex_match(string $p_str, string $p_regex): bool {
        return (bool)preg_match($p_regex, $p_str);
    }

    // --------------------------------------------------------------------

    /**
     * Match one field to another
     *
     * @param string $p_str string to compare against
     * @param string $p_field the field name that contains the value to match.
     *
     * @return    bool
     */
    public function matches(string $p_str, string $p_field): bool {
        return isset($this->_data[$p_field]) && $p_str === $this->_data[$p_field];
    }

    // --------------------------------------------------------------------

    /**
     * Differs from another field
     *
     * @param string $p_str string to compare against
     * @param string $p_field the field name that contains the value to check.
     *
     * @return    bool
     */
    public function differs(string $p_str, string $p_field): bool {
        return !$this->matches($p_str, $p_field);
    }


    // --------------------------------------------------------------------

    /**
     * Minimum Length
     *
     * @param string $p_str the value to check
     * @param string $p_val with the min length allowed
     *
     * @return    bool
     */
    public function min_length(string $p_str, string $p_val): bool {
        if (!is_numeric($p_val)) {
            return false;
        }

        return ($p_val <= mb_strlen($p_str));
    }

    // --------------------------------------------------------------------

    /**
     * Max Length
     *
     * @param string $p_str with the value to check
     * @param string $p_val with the max length allowed
     *
     * @return    bool
     */
    public function max_length(string $p_str, string $p_val): bool {
        if (!is_numeric($p_val)) {
            return false;
        }

        return ($p_val >= mb_strlen($p_str));
    }

    // --------------------------------------------------------------------

    /**
     * Exact Length
     *
     * @param string $p_str with the value to check
     * @param string $p_val with the exact length allowed
     *
     * @return    bool
     */
    public function exact_length(string $p_str, string $p_val): bool {
        if (!is_numeric($p_val)) {
            return false;
        }

        return (mb_strlen($p_str) === (int)$p_val);
    }

    // --------------------------------------------------------------------

    /**
     * Valid URL
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function valid_url(string $p_str): bool {
        if (empty($p_str)) {
            return false;
        } elseif (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', $p_str, $matches)) {
            if (empty($matches[2])) {
                return false;
            } elseif (!in_array(strtolower($matches[1]), ['http', 'https', 'ftp'], true)) {
                return false;
            }

            $p_str = $matches[2];
        }

        // PHP 7 accepts IPv6 addresses within square brackets as hostnames,
        // but it appears that the PR that came in with https://bugs.php.net/bug.php?id=68039
        // was never merged into a PHP 5 branch ... https://3v4l.org/8PsSN
        if (preg_match('/^\[([^\]]+)\]/', $p_str, $matches) && !is_php('7') && filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false) {
            $p_str = 'ipv6.host'.substr($p_str, strlen($matches[1]) + 2);
        }

        return (filter_var('http://'.$p_str, FILTER_VALIDATE_URL) !== false);
    }

    // --------------------------------------------------------------------

    /**
     * Valid Email
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function valid_email(string $p_str): bool {
        if (function_exists('idn_to_ascii') && sscanf($p_str, '%[^@]@%s', $name, $domain) === 2) {
            $p_str = $name.'@'.idn_to_ascii($domain);
        }

        return (bool)filter_var($p_str, FILTER_VALIDATE_EMAIL);
    }

    // --------------------------------------------------------------------

    /**
     * Valid Emails , multiple comma separated
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function valid_emails(string $p_str): bool {
        if (strpos($p_str, ',') === false) {
            return $this->valid_email(trim($p_str));
        }

        foreach (explode(',', $p_str) as $email) {
            if (trim($email) !== '' && $this->valid_email(trim($email)) === false) {
                return false;
            }
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Validate IP Address
     *
     * @param string $p_ip IP address
     * @param string $p_which IP protocol: 'ipv4' or 'ipv6'
     *
     * @return    bool
     */
    public function valid_ip(string $p_ip, string $p_which = ''): bool {
        return flcStrUtils::valid_ip($p_ip, $p_which);
    }

    // --------------------------------------------------------------------

    /**
     * Alpha
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function alpha(string $p_str): bool {
        return ctype_alpha($p_str);
    }

    // --------------------------------------------------------------------

    /**
     * Alpha-numeric
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function alpha_numeric(string $p_str): bool {
        return ctype_alnum($p_str);
    }

    // --------------------------------------------------------------------

    /**
     * Alpha-numeric w/ spaces
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function alpha_numeric_spaces(string $p_str): bool {
        if (!preg_match('/^[A-Z0-9 ]+$/i', $p_str)) {
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Alpha-numeric with underscores and dashes
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function alpha_dash(string $p_str): bool {
        if (!preg_match('/^[a-z0-9_-]+$/i', $p_str)) {
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Numeric
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function numeric(string $p_str): bool {
        if (!preg_match('/^[\-+]?[0-9]*\.?[0-9]+$/', $p_str)) {
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Integer
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function integer(string $p_str): bool {
        if (!preg_match('/^[\-+]?[0-9]+$/', $p_str)) {
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Decimal number
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function decimal(string $p_str): bool {
        if (!preg_match('/^[-+]?[0-9]*\.?[0-9]*$/', $p_str)) {
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Greater than
     *
     * @param string $p_str the value to check
     * @param string $p_min the min value
     *
     * @return    bool
     */
    public function greater_than(string $p_str, string $p_min): bool {
        return is_numeric($p_str) && $p_str > $p_min;
    }



    // --------------------------------------------------------------------

    /**
     * Equal to or Greater than
     *
     * @param string $p_str the value to check
     * @param int    $p_min the min value
     *
     * @return    bool
     */
    public function greater_than_equal_to(string $p_str, int $p_min): bool {
        return is_numeric($p_str) && $p_str >= $p_min;
    }

    // --------------------------------------------------------------------

    /**
     * Less than
     *
     * @param string $p_str the value to check
     * @param int    $p_max the max value
     *
     * @return    bool
     */
    public function less_than(string $p_str, int $p_max): bool {
        return is_numeric($p_str) && $p_str < $p_max;
    }

    // --------------------------------------------------------------------

    /**
     * Equal to or Less than
     *
     * @param string $p_str the value to check
     * @param int    $p_max the max value
     *
     * @return    bool
     */
    public function less_than_equal_to(string $p_str, int $p_max): bool {
        return is_numeric($p_str) && $p_str <= $p_max;
    }

    // --------------------------------------------------------------------

    /**
     * Value should be within an array of values
     *
     * @param string $p_value the value to search
     * @param string $p_list the list of values to search on (string with comma separated values).
     *
     * @return    bool
     */
    public function in_list(string $p_value, string $p_list): bool {
        return in_array($p_value, explode(',', $p_list), true);
    }

    // --------------------------------------------------------------------

    /**
     * Is a Natural number  (0,1,2,3, etc.)
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function is_natural(string $p_str): bool {
        return ctype_digit($p_str);
    }

    // --------------------------------------------------------------------

    /**
     * Is a Natural number, but not a zero  (1,2,3, etc.)
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function is_natural_no_zero(string $p_str): bool {
        return ($p_str != 0 && ctype_digit($p_str));
    }

    // --------------------------------------------------------------------

    /**
     * Valid Base64
     *
     * Tests a string for characters outside of the Base64 alphabet
     * as defined by RFC 2045 http://www.faqs.org/rfcs/rfc2045
     *
     * @param string $p_str the value to check
     *
     * @return    bool
     */
    public function valid_base64(string $p_str): bool {
        return (base64_encode(base64_decode($p_str)) === $p_str);
    }

    // --------------------------------------------------------------------

    /**
     * Basically for the use of codes , allow begin and end with
     * letters or numbers ,can be mixed qit special characters like
     * -._/ and blank but can be consecutive repeated .
     *
     * @param string $p_value the value to check
     *
     * @return bool
     */
    public function valid_code(string $p_value): bool {
        $regx = '/^[A-Za-z0-9ñÑ]+([- _\/.ñÑ]*[A-Za-z0-9])+$/';
        // Not allow repeat special characters allowed
        $regex2 = '/[- ._\/]{2,}/'; // non repeated check
        if (preg_match($regx, $p_value)) {
            return !preg_match($regex2, $p_value);
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Greather than .
     *
     * The p_field parameter need to exist in _data previous to call this method.
     *
     * @param string $p_str with the value to check ig greater then $p_field
     * @param string $p_field field name to get the other value to check
     *
     * @return    bool
     */
    public function greater_than_field(string $p_str, string $p_field): bool {
        if (!isset($this->_data[$p_field])) {
            return false;
        }

        $limit_date = $this->_data[$p_field];
        echo $limit_date;

        if (!is_numeric($p_str) || !is_numeric($limit_date)) {
            return false;
        }

        return $p_str > $limit_date;
    }

    // --------------------------------------------------------------------

    /**
     * valid date.
     *
     * Validate if a date is valid.
     * IF separator is '-' then the will be treated as d-m-y
     * otherwise as y-m-d
     *
     * @param string $p_date separated by '-' or '/' or '.' (not mixed)
     * @param bool   $p_verify_null si es true se verificara de lo contrario se asumira correcto
     *
     * @return bool true o false
     */
    public function valid_date(string $p_date, bool $p_verify_null = true): bool {


        if ($p_date === 'null') {
            if ($p_verify_null) {
                return false;
            } else {
                return true;
            }
        }

        // Get separator
        if (strpos($p_date, '.')) {
            $sep = '.';
        } elseif (strpos($p_date, '-')) {
            $sep = '-';
        } else {
            $sep = '/';
        }


        // separated date parts
        $test_arr = explode($sep, $p_date);


        // depending on the separator get the parts
        // using php protocol / is for m/d/y ,- and others for d-m-y
        if (count($test_arr) == 3) {
            if ($sep != '/') {
                $d = $test_arr[2];
                $m = $test_arr[1];
                $y = $test_arr[0];
            } else {
                $d = $test_arr[0];
                $m = $test_arr[1];
                $y = $test_arr[2];

            }

            // check
            if (checkdate($m, $d, $y)) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Less than.
     *
     * The p_field parameter need to exist in _data previous to call this method.
     *
     * @param string $p_str with the value to check is less then $p_field
     * @param string $p_field field name to get the other value to check
     *
     * @return    bool
     */

    public function less_than_field(string $p_str, string $p_field): bool {
        if (!isset($this->_data[$p_field])) {
            return false;
        }

        $limit_date = $this->_data[$p_field];

        if (!is_numeric($p_str) || !is_numeric($limit_date)) {
            return false;
        }

        return $p_str < $limit_date;
    }

    // --------------------------------------------------------------------

    /**
     * is future date.
     * Verify is a date is greater than other.
     *
     * The p_field parameter need to exist in _data previous to call this method.
     *
     * @param string $p_str the date to check.
     * @param string $p_field the field name with the limit date.
     *
     * @return bool
     */
    public function is_future_date(string $p_str, string $p_field): bool {
        if (!isset($this->_data[$p_field])) {
            return false;
        }

        $limit_date = strtotime($this->_data[$p_field]);
        $check_date = strtotime($p_str);
        echo 'f1= '.$check_date.' and f2= '.$limit_date.PHP_EOL;

        if (!$limit_date || !$check_date) {
            return false;
        }

        echo 'f1= '.$check_date.' and f2= '.$limit_date.PHP_EOL;

        return $check_date > $limit_date;
    }

    // --------------------------------------------------------------------

    /**
     * is future or equal date.
     * Verify is a date is greater than or equal to other.
     *
     * The p_field parameter need to exist in _data previous to call this method.
     *
     * @param string $p_str the date to check.
     * @param string $p_field the field name with the limit date.
     *
     * @return bool
     */
    public function is_future_or_equal_date(string $p_str, string $p_field): bool {
        if (!isset($this->_data[$p_field])) {
            return false;
        }

        $limit_date = strtotime($this->_data[$p_field]);
        $check_date = strtotime($p_str);
        if (!$limit_date || !$check_date) {
            return false;
        }

        return $check_date >= $limit_date;
    }

    // --------------------------------------------------------------------

    /**
     * Verify if a field is valid when other field represented by p_field
     * is boolean and is true.
     *
     * The value of the parameter identified by p_field can be :
     * true,'true','TRUE',1,'1' to be treated as boolean true.
     *
     * The p_field parameter need to exist in _data previous to call this method.
     *
     * @param string $p_str the value of the field dependant , is not used by required by
     * protocol.
     * @param mixed  $p_field the fieldname of the field that neeed to be true.
     *
     * @return boolean true si es valido
     *
     */
    public function depends_on_boolean(string $p_str, $p_field): bool {
        if (isset($this->_data[$p_field])) {
            $value = $this->_data[$p_field];
            if ($value === true || $value === 'true' || $value === 'TRUE' || $value === '1' || $value === 1) {
                return true;
            }
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Verify if the field value is boolean, in this case , the values
     * true,'true','TRUE',1,'1' will be treated as boolean true.
     *
     * standard is_bool is not sufficient fot this .
     *
     * @param mixed $p_value value to check is boolean
     *
     * @return bool true or false
     */
    function is_boolean($p_value): bool {
        if ($p_value === true || $p_value === 'true' || $p_value === 'TRUE' || $p_value === false || $p_value === 'false' || $p_value === 'FALSE' || $p_value === '0' || $p_value === 0 || $p_value === '1' || $p_value === 1) {
            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

}

