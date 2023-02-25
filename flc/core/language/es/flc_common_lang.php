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

$lang['flc_common_not_logged_in']		= ' Usuario debe reingresar al sistema , tiempo de sesion vencido';

// persistence stuff
$lang['flc_common_record_not_exist'] = 'El registro no existe o fue eliminado por otra estacion';
$lang['flc_common_record_exist'] = ' El registro ya existe, refresque sus datos';
$lang['flc_common_record_modified'] = 'El registro fue modificado posiblemente desde otra estacion, refresque sus datos';
$lang['flc_common_duplicate_key'] = 'Ya existe un registro con el mismo codigo';
$lang['flc_common_foreign_key'] = 'La operacion no puede realizarse ya que el registro esta referenciado o necesita referenciar otras tablas';


// upload stuff
$lang['UPLOAD_ERR_INI_SIZE'] = 'El archivo a cargar excede el maximo permitido por el servidor';
$lang['UPLOAD_ERR_FORM_SIZE'] = 'El archivo a cargar excede el maximo permitido por el lado cliente';
$lang['UPLOAD_ERR_PARTIAL'] = 'El archivo fue parcialmente cargado , se ha cancelado la operacion';
$lang['UPLOAD_ERR_NO_FILE'] = 'No se ha cargado archivo alguno';
$lang['UPLOAD_ERR_NO_TMP_DIR'] = 'No se ha indicado el directorio temporal o no existe';
$lang['UPLOAD_ERR_CANT_WRITE'] = 'Error grabando el archivo';
$lang['UPLOAD_ERR_EXTENSION'] = 'Alguna extension del server no permitio la carga del archivo';
$lang['UPLOAD_ERR_NO_FILE'] = 'No hay archivo a cargar';
$lang['C_UPLOAD_ERR_MAX_SIZE'] = 'El archivo execede el tamaño maximo permitido';
$lang['C_UPLOAD_MIME_NOT_ALLOWED'] = 'El tipo de archivo no esta permitido para carga';
$lang['C_UPLOAD_DESTINATION_ERROR'] = 'No pudo grabarse el archivo en el directorio destino';
$lang['C_UPLOAD_NO_FILEPATH'] = 'No se ha indicado directorio destino';
$lang['C_UPLOAD_FILEPATH_NOT_WRITABLE'] = 'El directorio destino esta en modo solo lectura';