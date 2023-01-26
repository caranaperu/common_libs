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
defined('BASEPATH') OR exit('No direct script access allowed');

$lang['validation_required']		= ' El campo {field} es obligatorio.';
$lang['validation_isset']			= ' El campo {field} debe terner un valor indicado.'; // standard php function
$lang['validation_valid_email']		= ' El campo {field} debe contener un email valido.';
$lang['validation_valid_emails']	= ' El campo {field} debe contener solo emails validos.';
$lang['validation_valid_url']		= ' El campo {field} debe contener un URL valido.';
$lang['validation_valid_ip']		= ' El campo {field} debe contener un IP valido.';
$lang['validation_min_length']		= ' El campo {field} debe tener al menos {param} caracteres de longitud.';
$lang['validation_max_length']		= ' El campo {field} no debe exceder {param} caracteres de longitud.';
$lang['validation_exact_length']	= ' El campo {field} debe tener exactamente {param} caracteres de longitud.';
$lang['validation_alpha']			= ' El campo {field} solo debe contener caraccters alfabeticos.';
$lang['validation_alpha_numeric']	= ' El campo {field} solo debe contener caracteres alfanumericos.';
$lang['validation_alpha_numeric_spaces']	= ' El campo {field} solo debe contener caracteres alfanumericos y espacios.';
$lang['validation_alpha_dash']		= ' El campo {field} solo debe contener caracteres alfanumericos, underscores, y guiones.';
$lang['validation_numeric']		= ' El campo {field} solo debe contener numeros.';
$lang['validation_is_numeric']		= ' El campo {field} solo debe contener caracters numericos.';
$lang['validation_integer']		= ' El campo {field} solo debe contener un entero.';
$lang['validation_regex_match']		= ' El campo {field} no tiene el formato correcto.';
$lang['validation_matches']		= ' El campo {field} no coincide con el capo {param}.';
$lang['validation_differs']		= ' El campo {field} debe ser diferente al campo {param}.';
$lang['validation_is_natural']		= ' El campo {field} solo debe contener digitos.';
$lang['validation_is_natural_no_zero']	= ' El campo {field} solo debe contener digitos y debe ser mayor que cero.';
$lang['validation_decimal']		= ' El campo {field} debe contener un numero decimal.';
$lang['validation_less_than']		= ' El campo {field} debe ser menor que {param}.';
$lang['validation_less_than_equal_to']	= ' El campo {field} debe contener un numero menor o igual a {param}.';
$lang['validation_greater_than']		= ' El campo {field} debe contener un numero mayor a {param}.';//
$lang['validation_greater_than_equal_to']	= ' El campo {field}  contener un numero mayor o igual a {param}.';
$lang['validation_in_list']		= ' El campo {field} debe ser uno de : {param}.';
$lang['validation_greater_than_field'] = ' El campo {field} debe ser mayor que el valor del campo {param}.';
$lang['validation_less_than_field'] = ' El campo {field} debe ser menor que el valor del campo {param}.';
$lang['validation_is_future_date'] = 'El campo {field} debe ser mayor al campo {param}.';
$lang['validation_is_future_or_equal_date'] = 'El campo {field} debe ser mayor o igual al campo {param}.';
$lang['validation_depends_on_boolean'] = 'El campo {field} requere qie el campo {param} sea verdadero.';
$lang['validation_is_boolean'] = 'El campo {field} debe ser un booleano';
$lang['validation_valid_code'] = 'El campo {field} solo debe contener caraceters alfanumericos y/o  los caracteres ./-_';
$lang['validation_valid_date'] = 'El campo {field} no es una fecha valida';
$lang['validation_valid_base64'] = 'El campo {field} noe es un valid base64';
$lang['validation_error_message_not_set']	= 'No se puede acceder al mensaje de validacion para el campo {field}.';
