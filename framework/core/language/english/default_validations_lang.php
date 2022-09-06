<?php
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
 * Important : This code is a modified one of Form_validation from codeigniter
 * all credits for his authors.
 *
 * @package    FLabsCode
 * @author    Carlos Arana
 * @copyright    Copyright (c) 2022 - 2022, FLabsCode
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://flabscorpprods.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['validation_required']		= 'The {field} field is required.';
$lang['validation_isset']			= 'The {field} field must have a value.'; // standard php function
$lang['validation_valid_email']		= 'The {field} field must contain a valid email address.';
$lang['validation_valid_emails']	= 'The {field} field must contain all valid email addresses.';
$lang['validation_valid_url']		= 'The {field} field must contain a valid URL.';
$lang['validation_valid_ip']		= 'The {field} field must contain a valid IP.';
$lang['validation_min_length']		= 'The {field} field must be at least {param} characters in length.';
$lang['validation_max_length']		= 'The {field} field cannot exceed {param} characters in length.';
$lang['validation_exact_length']	= 'The {field} field must be exactly {param} characters in length.';
$lang['validation_alpha']			= 'The {field} field may only contain alphabetical characters.';
$lang['validation_alpha_numeric']	= 'The {field} field may only contain alpha-numeric characters.';
$lang['validation_alpha_numeric_spaces']	= 'The {field} field may only contain alpha-numeric characters and spaces.';
$lang['validation_alpha_dash']		= 'The {field} field may only contain alpha-numeric characters, underscores, and dashes.';
$lang['validation_numeric']		= 'The {field} field must contain only numbers.';
$lang['validation_is_numeric']		= 'The {field} field must contain only numeric characters.';
$lang['validation_integer']		= 'The {field} field must contain an integer.';
$lang['validation_regex_match']		= 'The {field} field is not in the correct format.';
$lang['validation_matches']		= 'The {field} field does not match the {param} field.';
$lang['validation_differs']		= 'The {field} field must differ from the {param} field.';
$lang['validation_is_natural']		= 'The {field} field must only contain digits.';
$lang['validation_is_natural_no_zero']	= 'The {field} field must only contain digits and must be greater than zero.';
$lang['validation_decimal']		= 'The {field} field must contain a decimal number.';
$lang['validation_less_than']		= 'The {field} field must contain a number less than {param}.';
$lang['validation_less_than_equal_to']	= 'The {field} field must contain a number less than or equal to {param}.';
$lang['validation_greater_than']		= 'The {field} field must contain a number greater than {param}.';//
$lang['validation_greater_than_equal_to']	= 'The {field} field must contain a number greater than or equal to {param}.';
$lang['validation_in_list']		= 'The {field} field must be one of: {param}.';
$lang['validation_greater_than_field'] = 'The field {field} cant be lesser than the value of field {param}.';
$lang['validation_less_than_field'] = 'The field {field} cant be greater than the value of field {param}.';
$lang['validation_is_future_date'] = ' {field} field need to be greater than {param} field.';
$lang['validation_is_future_or_equal_date'] = ' {field} field need to be greater than or equal to {param} param.';
$lang['validation_depends_on_boolean'] = ' {field} field requires that {param} be true.';
$lang['validation_is_boolean'] = ' {field} field must be a boolean';
$lang['validation_valid_code'] = ' {field} field must contain only alphanumeric and/or  ./-_ characers';
$lang['validation_valid_date'] = ' {field} field is not a valid date';
$lang['validation_valid_base64'] = ' {field} field is not a valid base64';
$lang['validation_error_message_not_set']	= 'Unable to access an error message corresponding to your field name {field}.';
