<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2023-03-12 04:04:39 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-12 03:04:40 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-12 03:04:40 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-12 03:04:40 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-12 03:04:40 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-12 03:04:40 --> Input Class Initialized
ERROR - 2023-03-12 03:09:16 --> Error executing fetch full : 0 - pg_query(): Query failed: ERROR:  missing FROM-clause entry for table &quot;tb_atletas&quot;
LINE 1: ...VARYING AS atletas_agno from  tb_atletas) a where tb_atletas...
                                                             ^ - ERROR:  missing FROM-clause entry for table "tb_atletas"
LINE 1: ...VARYING AS atletas_agno from  tb_atletas) a where tb_atletas...
                                                             ^(0)
DEBUG - 2023-03-12 04:09:23 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-12 03:09:23 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-12 03:09:23 --> flcUtf8->_construct - Utf8 Class Initialized
DEBUG - 2023-03-12 04:09:23 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-12 03:09:23 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-12 03:09:23 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-12 03:09:23 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-12 03:09:23 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-12 03:09:23 --> Input Class Initialized
INFO - 2023-03-12 03:09:23 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
INFO - 2023-03-12 03:09:23 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-12 03:09:23 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-12 03:09:23 --> Input Class Initialized
DEBUG - 2023-03-12 04:09:43 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-12 03:09:43 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-12 03:09:43 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-12 03:09:43 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-12 03:09:43 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-12 03:09:43 --> Input Class Initialized
DEBUG - 2023-03-12 04:09:53 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-12 03:09:53 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-12 03:09:53 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-12 03:09:53 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-12 03:09:53 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-12 03:09:53 --> Input Class Initialized
INFO - 2023-03-12 03:09:53 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ghg',atletas_nombres='fghfghfghfghfh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='NOR',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular=NULL,atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-02-03 03:54:23.419107' where atletas_codigo='ERTERT'
INFO - 2023-03-12 03:09:53 --> affected rows update 1
INFO - 2023-03-12 03:09:53 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-12 04:10:06 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-12 03:10:06 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-12 03:10:06 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-12 03:10:06 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-12 03:10:06 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-12 03:10:06 --> Input Class Initialized
INFO - 2023-03-12 03:10:06 --> update tb_atletas set atletas_codigo='ERTERT',atletas_ap_paterno='ertert',atletas_ap_materno='ghg',atletas_nombres='fghfghfghfghfh',atletas_sexo='M',atletas_nro_documento='09988990',atletas_nro_pasaporte='99999999',paises_codigo='NOR',atletas_fecha_nacimiento='1990-12-29',atletas_telefono_casa='2756910',atletas_telefono_celular='9999999999',atletas_email=NULL,atletas_direccion='rtyrtyryryrerytrtyrty',atletas_observaciones=NULL,atletas_talla_ropa_buzo='M',atletas_talla_ropa_poloshort='M',atletas_talla_zapatillas=NULL,atletas_norma_zapatillas='UK',atletas_url_foto=NULL,activo='1',usuario='postgres',fecha_creacion='2023-01-28 14:09:23.985182',usuario_mod='postgres',fecha_modificacion='2023-02-03 03:54:23.419107' where atletas_codigo='ERTERT'
INFO - 2023-03-12 03:10:06 --> affected rows update 1
INFO - 2023-03-12 03:10:06 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='ERTERT'
DEBUG - 2023-03-12 04:22:07 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-12 03:22:07 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-12 03:22:07 --> flcUtf8->_construct - Utf8 Class Initialized
DEBUG - 2023-03-12 04:22:07 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-12 03:22:07 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-12 03:22:07 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-12 03:22:07 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-12 03:22:07 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-12 03:22:07 --> Input Class Initialized
INFO - 2023-03-12 03:22:07 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
INFO - 2023-03-12 03:22:07 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-12 03:22:07 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-12 03:22:07 --> Input Class Initialized
