<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2023-03-25 01:47:22 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:47:22 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:47:22 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:47:22 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:47:22 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:47:22 --> Input Class Initialized
INFO - 2023-03-25 00:47:22 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='NOR',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999999',atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-24 18:29:05.583468' where atletas_codigo='ERTERT' and xmin = 10860
INFO - 2023-03-25 00:47:22 --> affected rows update 0
INFO - 2023-03-25 00:47:22 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 01:47:53 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:47:53 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:47:53 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:47:53 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:47:53 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:47:53 --> Input Class Initialized
ERROR - 2023-03-25 00:47:53 --> Field 'xmin' is not part of the model
DEBUG - 2023-03-25 01:49:44 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:49:44 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:49:44 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:49:44 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:49:44 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:49:44 --> Input Class Initialized
INFO - 2023-03-25 00:49:44 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises where tb_paises.paises_codigo ilike 'PER%' order by paises_codigo limit 125 offset 0
DEBUG - 2023-03-25 01:49:46 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:49:46 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:49:46 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:49:46 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:49:46 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:49:46 --> Input Class Initialized
INFO - 2023-03-25 00:49:46 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises where tb_paises.paises_codigo ilike 'PER%' order by paises_codigo limit 125 offset 0
DEBUG - 2023-03-25 01:49:55 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:49:55 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:49:55 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:49:55 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:49:55 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:49:55 --> Input Class Initialized
INFO - 2023-03-25 00:49:55 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 01:50:53 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:50:53 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:50:53 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:50:53 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:50:53 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:50:53 --> Input Class Initialized
INFO - 2023-03-25 00:51:17 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9998999999',atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-24 18:29:05.583468' where atletas_codigo='ERTERT' and xmin = 10864
INFO - 2023-03-25 00:51:25 --> affected rows update 0
INFO - 2023-03-25 00:52:04 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 01:56:18 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:56:18 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:56:18 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:56:18 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:56:18 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:56:18 --> Input Class Initialized
INFO - 2023-03-25 00:56:28 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9998999999',atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-24 18:29:05.583468' where atletas_codigo='ERTERT' and xmin = 10864
INFO - 2023-03-25 00:56:33 --> affected rows update 0
INFO - 2023-03-25 00:57:07 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 01:59:32 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:59:32 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:59:32 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:59:32 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:59:32 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:59:32 --> Input Class Initialized
INFO - 2023-03-25 00:59:32 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9998999999',atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-24 18:29:05.583468' where atletas_codigo='ERTERT' and xmin = 10864
INFO - 2023-03-25 00:59:32 --> affected rows update 0
INFO - 2023-03-25 00:59:32 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 01:59:39 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 00:59:39 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 00:59:39 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 00:59:39 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 00:59:39 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 00:59:39 --> Input Class Initialized
INFO - 2023-03-25 00:59:39 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 02:00:10 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 01:00:10 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 01:00:10 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 01:00:10 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 01:00:10 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 01:00:10 --> Input Class Initialized
INFO - 2023-03-25 01:00:10 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='NOR',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='8989898998',atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-24 18:29:05.583468' where atletas_codigo='ERTERT' and xmin = 10865
INFO - 2023-03-25 01:00:10 --> affected rows update 0
INFO - 2023-03-25 01:00:10 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 02:01:44 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 01:01:44 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 01:01:44 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 01:01:44 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 01:01:45 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 01:01:45 --> Input Class Initialized
DEBUG - 2023-03-25 02:01:48 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 01:01:48 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 01:01:48 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 01:01:48 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 01:01:48 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 01:01:48 --> Input Class Initialized
INFO - 2023-03-25 01:01:48 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 02:02:20 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 01:02:20 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 01:02:20 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 01:02:20 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 01:02:20 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 01:02:20 --> Input Class Initialized
INFO - 2023-03-25 01:02:20 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='8888888888',atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 06:00:00.185719' where atletas_codigo='ERTERT' and xmin = 10866
INFO - 2023-03-25 01:02:20 --> affected rows update 0
INFO - 2023-03-25 01:02:20 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 02:02:23 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 01:02:23 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 01:02:23 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 01:02:23 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 01:02:23 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 01:02:23 --> Input Class Initialized
INFO - 2023-03-25 01:02:23 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='8888888888',atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 06:00:00.185719' where atletas_codigo='ERTERT' and xmin = 10866
INFO - 2023-03-25 01:02:23 --> affected rows update 0
INFO - 2023-03-25 01:02:23 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 02:19:39 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 01:19:39 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 01:19:39 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 01:19:39 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 01:19:39 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 01:19:39 --> Input Class Initialized
INFO - 2023-03-25 01:19:39 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 03:03:26 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:03:26 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:03:26 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:03:26 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:03:26 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:03:26 --> Input Class Initialized
DEBUG - 2023-03-25 03:03:29 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:03:29 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:03:29 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:03:29 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:03:29 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:03:29 --> Input Class Initialized
INFO - 2023-03-25 02:03:29 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 03:03:39 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:03:39 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:03:39 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:03:39 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:03:39 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:03:39 --> Input Class Initialized
INFO - 2023-03-25 02:03:39 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999802',atletas_email='',atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 06:02:08.601907' where atletas_codigo='ERTERT' and xmin = 10867
INFO - 2023-03-25 02:03:39 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:04:05 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:04:05 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:04:05 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:04:05 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:04:05 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:04:05 --> Input Class Initialized
INFO - 2023-03-25 02:04:05 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999804',atletas_email='',atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 06:02:08.601907' where atletas_codigo='ERTERT' and xmin = 10868
INFO - 2023-03-25 02:04:05 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:04:17 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:04:17 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:04:17 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:04:17 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:04:17 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:04:17 --> Input Class Initialized
INFO - 2023-03-25 02:04:17 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999804',atletas_email='',atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 06:02:08.601907' where atletas_codigo='ERTERT' and xmin = 10868
INFO - 2023-03-25 02:04:17 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:04:33 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:04:33 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:04:33 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:04:33 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:04:33 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:04:33 --> Input Class Initialized
DEBUG - 2023-03-25 03:04:36 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:04:36 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:04:36 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:04:36 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:04:36 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:04:36 --> Input Class Initialized
INFO - 2023-03-25 02:04:36 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 03:04:42 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:04:42 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:04:42 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:04:42 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:04:42 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:04:42 --> Input Class Initialized
INFO - 2023-03-25 02:04:42 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999804',atletas_email='',atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 07:03:55.695617' where atletas_codigo='ERTERT' and xmin = 10869
INFO - 2023-03-25 02:04:42 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:05:39 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:05:39 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:05:39 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:05:39 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:05:39 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:05:39 --> Input Class Initialized
INFO - 2023-03-25 02:05:39 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999805',atletas_email='',atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 07:03:55.695617' where atletas_codigo='ERTERT' and xmin = 10870
INFO - 2023-03-25 02:05:39 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:05:57 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:05:57 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:05:57 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:05:57 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:05:57 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:05:57 --> Input Class Initialized
INFO - 2023-03-25 02:05:57 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 03:06:17 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:06:17 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:06:17 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:06:17 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:06:17 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:06:17 --> Input Class Initialized
INFO - 2023-03-25 02:06:17 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999806',atletas_email='',atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 07:03:55.695617' where atletas_codigo='ERTERT' and xmin = 10871
INFO - 2023-03-25 02:06:17 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:06:29 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:06:29 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:06:29 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:06:29 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:06:29 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:06:29 --> Input Class Initialized
INFO - 2023-03-25 02:06:29 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ff',atletas_nombres='fghfghfhfgfghh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='PER',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999808',atletas_email='',atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-03-25 07:03:55.695617' where atletas_codigo='ERTERT' and xmin = 10872
INFO - 2023-03-25 02:06:29 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:07:08 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:07:08 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:07:08 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:07:08 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:07:08 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:07:08 --> Input Class Initialized
INFO - 2023-03-25 02:07:08 --> delete from tb_atletas where atletas_codigo='ERTERT' and xmin = 10873
INFO - 2023-03-25 02:07:08 --> affected rows delete 0
INFO - 2023-03-25 02:07:08 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:09:08 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:09:08 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:09:08 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:09:08 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:09:08 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:09:08 --> Input Class Initialized
DEBUG - 2023-03-25 03:09:15 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:09:15 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:09:15 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:09:15 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:09:15 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:09:15 --> Input Class Initialized
INFO - 2023-03-25 02:09:15 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 03:09:22 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:09:22 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:09:22 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:09:22 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:09:22 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:09:22 --> Input Class Initialized
INFO - 2023-03-25 02:09:22 --> delete from tb_atletas where atletas_codigo='ERTERT' and xmin = 10874
INFO - 2023-03-25 02:09:22 --> affected rows delete 1
ERROR - 2023-03-25 02:09:22 --> Typed property flc\core\accessor\flcPersistenceAccessorAnswer::$return_code must not be accessed before initialization
DEBUG - 2023-03-25 03:10:09 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:10:09 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:10:09 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:10:09 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:10:09 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:10:09 --> Input Class Initialized
INFO - 2023-03-25 02:10:09 --> delete from tb_atletas where atletas_codigo='ERTERT' and xmin = 10874
INFO - 2023-03-25 02:10:09 --> affected rows delete 0
INFO - 2023-03-25 02:10:09 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-25 03:10:26 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:10:26 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:10:26 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:10:26 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:10:26 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:10:26 --> Input Class Initialized
DEBUG - 2023-03-25 03:11:00 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:11:00 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:11:00 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:11:00 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:11:00 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:11:00 --> Input Class Initialized
DEBUG - 2023-03-25 03:11:13 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:11:13 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:11:13 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:11:13 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:11:13 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:11:13 --> Input Class Initialized
INFO - 2023-03-25 02:11:13 --> delete from tb_atletas where atletas_codigo='09753034' and xmin = 10877
INFO - 2023-03-25 02:11:13 --> affected rows delete 1
ERROR - 2023-03-25 02:11:13 --> Typed property flc\core\accessor\flcPersistenceAccessorAnswer::$return_code must not be accessed before initialization
DEBUG - 2023-03-25 03:11:19 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:11:19 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:11:19 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:11:19 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:11:19 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:11:19 --> Input Class Initialized
INFO - 2023-03-25 02:11:19 --> delete from tb_atletas where atletas_codigo='09753034' and xmin = 10877
INFO - 2023-03-25 02:11:19 --> affected rows delete 0
INFO - 2023-03-25 02:11:19 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='09753034'
DEBUG - 2023-03-25 03:11:23 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:11:23 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:11:23 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:11:23 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:11:23 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:11:23 --> Input Class Initialized
INFO - 2023-03-25 02:11:23 --> delete from tb_atletas where atletas_codigo='09753034' and xmin = 10877
INFO - 2023-03-25 02:11:23 --> affected rows delete 0
INFO - 2023-03-25 02:11:23 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='09753034'
DEBUG - 2023-03-25 03:11:27 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:11:27 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:11:27 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:11:27 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:11:27 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:11:27 --> Input Class Initialized
DEBUG - 2023-03-25 03:12:09 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:12:09 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:12:09 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:12:09 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:12:09 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:12:09 --> Input Class Initialized
DEBUG - 2023-03-25 03:12:43 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:12:43 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:12:43 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:12:43 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:12:43 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:12:43 --> Input Class Initialized
INFO - 2023-03-25 02:12:52 --> delete from tb_atletas where atletas_codigo='xxxxxxx' and xmin = 10879
INFO - 2023-03-25 02:13:12 --> affected rows delete 1
ERROR - 2023-03-25 02:14:51 --> Typed property flc\core\accessor\flcPersistenceAccessorAnswer::$return_code must not be accessed before initialization
DEBUG - 2023-03-25 03:15:00 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:15:00 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:15:00 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:15:00 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:15:00 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:15:00 --> Input Class Initialized
DEBUG - 2023-03-25 03:15:29 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:15:29 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:15:29 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:15:29 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:15:29 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:15:29 --> Input Class Initialized
DEBUG - 2023-03-25 03:15:36 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:15:36 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:15:36 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:15:36 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:15:36 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:15:36 --> Input Class Initialized
INFO - 2023-03-25 02:15:36 --> delete from tb_atletas where atletas_codigo='xxxxx' and xmin = 10881
INFO - 2023-03-25 02:15:36 --> affected rows delete 1
DEBUG - 2023-03-25 03:16:03 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:16:03 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:16:03 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:16:03 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:16:03 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:16:03 --> Input Class Initialized
DEBUG - 2023-03-25 03:16:13 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:16:13 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:16:13 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:16:13 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:16:13 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:16:13 --> Input Class Initialized
INFO - 2023-03-25 02:16:13 --> delete from tb_atletas where atletas_codigo='xxxxx' and xmin = 10883
INFO - 2023-03-25 02:16:13 --> affected rows delete 0
INFO - 2023-03-25 02:16:13 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='xxxxx'
DEBUG - 2023-03-25 03:16:22 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:16:22 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:16:22 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:16:22 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:16:22 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:16:22 --> Input Class Initialized
INFO - 2023-03-25 02:16:22 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 03:16:28 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:16:28 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:16:28 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:16:28 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:16:28 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:16:28 --> Input Class Initialized
DEBUG - 2023-03-25 03:16:39 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:16:39 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:16:39 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:16:39 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:16:39 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:16:39 --> Input Class Initialized
INFO - 2023-03-25 02:16:39 --> update tb_atletas set atletas_codigo='xxxxx',atletas_ap_paterno='xxxxx',atletas_ap_materno='xxx',atletas_nombres='xxxx',atletas_sexo='F',atletas_nro_documento='56565656',atletas_nro_pasaporte='',paises_codigo='PER',atletas_fecha_nacimiento='1972-09-07',atletas_telefono_casa='3434343433',atletas_telefono_celular='7878787878',atletas_email='',atletas_direccion='??',atletas_observaciones='',atletas_talla_ropa_buzo='??',atletas_talla_ropa_poloshort='??',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='??',atletas_url_foto='',activo='1',usuario='atluser',fecha_creacion='2023-03-25 07:15:59.556401',usuario_mod='TESTUSER',fecha_modificacion='2023-02-04 00:03:21.863135' where atletas_codigo='xxxxx' and xmin = 10883
INFO - 2023-03-25 02:16:39 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='xxxxx'
DEBUG - 2023-03-25 03:16:44 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:16:44 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:16:44 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:16:44 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:16:44 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:16:44 --> Input Class Initialized
DEBUG - 2023-03-25 03:36:55 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:36:55 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:36:55 --> flcUtf8->_construct - Utf8 Class Initialized
DEBUG - 2023-03-25 03:36:55 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:36:55 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:36:55 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:36:55 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:36:55 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:36:55 --> Input Class Initialized
INFO - 2023-03-25 02:36:55 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
INFO - 2023-03-25 02:36:55 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:36:55 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:36:55 --> Input Class Initialized
DEBUG - 2023-03-25 03:37:41 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:37:41 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:37:41 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:37:41 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:37:41 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:37:41 --> Input Class Initialized
INFO - 2023-03-25 02:37:41 --> affected rows add 1
INFO - 2023-03-25 02:37:41 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='SDSD'
DEBUG - 2023-03-25 03:37:55 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:37:55 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:37:55 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:37:55 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:37:55 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:37:55 --> Input Class Initialized
INFO - 2023-03-25 02:37:55 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 03:38:01 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:38:01 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:38:01 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:38:01 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:38:01 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:38:01 --> Input Class Initialized
INFO - 2023-03-25 02:38:01 --> update tb_atletas set atletas_codigo='SDSD',atletas_ap_paterno='sdsd',atletas_ap_materno='sdsd',atletas_nombres='sds',atletas_sexo='M',atletas_nro_documento='08809876',atletas_nro_pasaporte='12111111',paises_codigo='BRA',atletas_fecha_nacimiento='1960-12-28',atletas_telefono_casa=NULL,atletas_telefono_celular=NULL,atletas_email=NULL,atletas_direccion='srwrwerwerwerwe',atletas_observaciones=NULL,atletas_talla_ropa_buzo='??',atletas_talla_ropa_poloshort='??',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='??',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-03-25 02:37:40.678901',usuario_mod=NULL,fecha_modificacion=NULL where atletas_codigo='SDSD' and xmin = 10885
INFO - 2023-03-25 02:38:01 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='SDSD'
DEBUG - 2023-03-25 03:38:09 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 02:38:09 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 02:38:09 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 02:38:09 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 02:38:09 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 02:38:09 --> Input Class Initialized
INFO - 2023-03-25 02:38:09 --> delete from tb_atletas where atletas_codigo='SDSD' and xmin = 10886
INFO - 2023-03-25 02:38:09 --> affected rows delete 1
DEBUG - 2023-03-25 04:20:04 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:20:04 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:20:04 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:20:04 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:20:04 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:20:04 --> Input Class Initialized
DEBUG - 2023-03-25 04:20:10 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:20:10 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:20:10 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:20:10 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:20:10 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:20:10 --> Input Class Initialized
INFO - 2023-03-25 03:20:10 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 04:20:24 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:20:24 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:20:24 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:20:24 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:20:24 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:20:24 --> Input Class Initialized
INFO - 2023-03-25 03:20:36 --> delete from tb_atletas where atletas_codigo='cccccccc' and xmin = 10888
INFO - 2023-03-25 03:20:38 --> affected rows delete 1
DEBUG - 2023-03-25 04:22:56 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:22:56 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:22:56 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:22:56 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:22:56 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:22:56 --> Input Class Initialized
INFO - 2023-03-25 03:22:55 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 04:23:04 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:23:04 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:23:04 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:23:04 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:23:04 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:23:04 --> Input Class Initialized
ERROR - 2023-03-25 03:23:40 --> Error executing add : 0 - pg_query(): Query failed: ERROR:  null value in column &quot;atletas_nro_pasaporte&quot; of relation &quot;tb_atletas&quot; violates not-null constraint
DETAIL:  Failing row contains (GGGGG, ggg, ggg, gggg, ggg ggg, gggg, M, 09900999, null, BOL, 1968-12-29, null, null, null, rwerwerwerwer, null, ??, ??, null, ??, null, t, postgres, 2023-03-25 03:23:19.102353, null, null, f).
CONTEXT:  SQL statement &quot;INSERT INTO
      tb_atletas
      (atletas_codigo, atletas_ap_paterno, atletas_ap_materno, atletas_nombres, atletas_nombre_completo,
       atletas_sexo, atletas_nro_documento, atletas_nro_pasaporte, paises_codigo,
       atletas_fecha_nacimiento, atletas_telefono_casa, atletas_telefono_celular, atletas_email, atletas_direccion,
       atletas_observaciones, atletas_talla_ropa_buzo, atletas_talla_ropa_poloshort, atletas_talla_zapatillas,
       atletas_norma_zapatillas, atletas_url_foto, activo, usuario_mod)
    VALUES (p_atletas_codigo,
      p_atletas_ap_paterno,
      p_atletas_ap_materno,
      p_atletas_nombres,
      v_nombre_completo,
      p_atletas_sexo,
      p_atletas_nro_documento,
      p_atletas_nro_pasaporte,
      p_paises_codigo,
      p_atletas_fecha_nacimiento,
      p_atletas_telefono_casa,
      p_atletas_telefono_celular,
      p_atletas_email,
      p_atletas_direccion,
      p_atletas_observaciones,
      p_atletas_talla_ropa_buzo,
      p_atletas_talla_ropa_poloshort,
      p_atletas_talla_zapatillas,
      p_atletas_norma_zapatillas,
      p_atletas_url_foto,
      p_activo,
            p_usuario)&quot;
PL/pgSQL function sp_atletas_save_record(character varying,character varying,character varying,character varying,character,character varying,character varying,character varying,date,character varying,character varying,character varying,character varying,character varying,character varying,character varying,numeric,character varying,character varying,boolean,character varying,integer,bit) line 146 at SQL statement - ERROR:  null value in column "atletas_nro_pasaporte" of relation "tb_atletas" violates not-null constraint
DETAIL:  Failing row contains (GGGGG, ggg, ggg, gggg, ggg ggg, gggg, M, 09900999, null, BOL, 1968-12-29, null, null, null, rwerwerwerwer, null, ??, ??, null, ??, null, t, postgres, 2023-03-25 03:23:19.102353, null, null, f).
CONTEXT:  SQL statement "INSERT INTO
      tb_atletas
      (atletas_codigo, atletas_ap_paterno, atletas_ap_materno, atletas_nombres, atletas_nombre_completo,
       atletas_sexo, atletas_nro_documento, atletas_nro_pasaporte, paises_codigo,
       atletas_fecha_nacimiento, atletas_telefono_casa, atletas_telefono_celular, atletas_email, atletas_direccion,
       atletas_observaciones, atletas_talla_ropa_buzo, atletas_talla_ropa_poloshort, atletas_talla_zapatillas,
       atletas_norma_zapatillas, atletas_url_foto, activo, usuario_mod)
    VALUES (p_atletas_codigo,
      p_atletas_ap_paterno,
      p_atletas_ap_materno,
      p_atletas_nombres,
      v_nombre_completo,
      p_atletas_sexo,
      p_atletas_nro_documento,
      p_atletas_nro_pasaporte,
      p_paises_codigo,
      p_atletas_fecha_nacimiento,
      p_atletas_telefono_casa,
      p_atletas_telefono_celular,
      p_atletas_email,
      p_atletas_direccion,
      p_atletas_observaciones,
      p_atletas_talla_ropa_buzo,
      p_atletas_talla_ropa_poloshort,
      p_atletas_talla_zapatillas,
      p_atletas_norma_zapatillas,
      p_atletas_url_foto,
      p_activo,
            p_usuario)"
PL/pgSQL function sp_atletas_save_record(character varying,character varying,character varying,character varying,character,character varying,character varying,character varying,date,character varying,character varying,character varying,character varying,character varying,character varying,character varying,numeric,character varying,character varying,boolean,character varying,integer,bit) line 146 at SQL statement(0)
DEBUG - 2023-03-25 04:23:57 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:23:57 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:23:57 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:23:57 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:23:57 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:23:57 --> Input Class Initialized
INFO - 2023-03-25 03:24:38 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='GGGGG'
DEBUG - 2023-03-25 04:25:31 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:25:31 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:25:31 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:25:31 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:25:31 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:25:31 --> Input Class Initialized
INFO - 2023-03-25 03:25:35 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
DEBUG - 2023-03-25 04:25:36 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:25:36 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:25:36 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:25:36 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:25:36 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:25:36 --> Input Class Initialized
INFO - 2023-03-25 03:25:59 --> update tb_atletas set atletas_codigo='GGGGG',atletas_ap_paterno='ggg',atletas_ap_materno='ggg',atletas_nombres='gggg',atletas_sexo='M',atletas_nro_documento='09900999',atletas_nro_pasaporte='76868686',paises_codigo='BOL',atletas_fecha_nacimiento='1968-12-29',atletas_telefono_casa=NULL,atletas_telefono_celular=NULL,atletas_email=NULL,atletas_direccion='rwerwerwerwer',atletas_observaciones=NULL,atletas_talla_ropa_buzo='??',atletas_talla_ropa_poloshort='??',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='??',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-03-25 03:24:13.134164',usuario_mod=NULL,fecha_modificacion=NULL where atletas_codigo='GGGGG' and xmin = 10890
INFO - 2023-03-25 03:26:14 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='GGGGG'
DEBUG - 2023-03-25 04:26:39 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-25 03:26:39 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-25 03:26:39 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-25 03:26:39 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-25 03:26:39 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-25 03:26:39 --> Input Class Initialized
INFO - 2023-03-25 03:26:40 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
