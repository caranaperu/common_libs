<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2022-12-26 00:26:40 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:26:42 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:26:42 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:26:46 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:26:46 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:26:46 --> Input Class Initialized
ERROR - 2022-12-26 00:28:19 --> 
Call to undefined function pront_r()
in /var/www/common/framework/tests/apptests/backend/views/paises_view.php on line 5.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 265 
Line: include 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 77 
Line: _get_view 

File: /var/www/common/framework/core/FLC.php 
Line: 359 
Line: service 

File: /var/www/common/framework/tests/apptests/backend/controllers/paises_controller.php 
Line: 44 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/apptests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
ERROR - 2022-12-26 00:28:21 --> 
Uncaught ErrorException: Cannot modify header information - headers already sent by (output started at /var/www/common/framework/core/accessor/flcDbAccessor.php:682) in /var/www/common/framework/core/flcMessageTrait.php:431
Stack trace:
#0 [internal function]: framework\utils\flcErrorHandlers->_error_handler()
#1 /var/www/common/framework/core/flcMessageTrait.php(431): header()
#2 /var/www/common/framework/utils/flcErrorHandlers.php(81): framework\core\flcResponse->set_status_header()
#3 [internal function]: framework\utils\flcErrorHandlers->_exception_handler()
#4 {main}
  thrown
in /var/www/common/framework/core/flcMessageTrait.php on line 431.
**********************************************************
DEBUG - 2022-12-26 00:28:22 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:28:22 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:28:22 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:28:23 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:28:23 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:28:23 --> Input Class Initialized
ERROR - 2022-12-26 00:28:24 --> Cant execute read - pg_query(): Query failed: ERROR:  column &quot;atletas_nombre&quot; does not exist
LINE 1: ...etas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_no...
                                                             ^
HINT:  Perhaps you meant to reference the column &quot;tb_atletas.atletas_nombres&quot;. / 0
DEBUG - 2022-12-26 00:28:24 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:28:25 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:28:25 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:28:25 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:28:25 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:28:25 --> Input Class Initialized
ERROR - 2022-12-26 00:28:26 --> 
Call to undefined function pront_r()
in /var/www/common/framework/tests/apptests/backend/views/paises_view.php on line 5.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 265 
Line: include 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 77 
Line: _get_view 

File: /var/www/common/framework/core/FLC.php 
Line: 359 
Line: service 

File: /var/www/common/framework/tests/apptests/backend/controllers/paises_controller.php 
Line: 44 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/apptests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
ERROR - 2022-12-26 00:28:27 --> 
Uncaught ErrorException: Cannot modify header information - headers already sent by (output started at /var/www/common/framework/core/accessor/flcDbAccessor.php:682) in /var/www/common/framework/core/flcMessageTrait.php:431
Stack trace:
#0 [internal function]: framework\utils\flcErrorHandlers->_error_handler()
#1 /var/www/common/framework/core/flcMessageTrait.php(431): header()
#2 /var/www/common/framework/utils/flcErrorHandlers.php(81): framework\core\flcResponse->set_status_header()
#3 [internal function]: framework\utils\flcErrorHandlers->_exception_handler()
#4 {main}
  thrown
in /var/www/common/framework/core/flcMessageTrait.php on line 431.
**********************************************************
DEBUG - 2022-12-26 00:31:36 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:31:38 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:31:38 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:31:39 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:31:39 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:31:39 --> Input Class Initialized
INFO - 2022-12-26 00:31:46 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombre,atletas_nombre_completo from tb_atletas order by atletas_codigo
ERROR - 2022-12-26 00:31:46 --> Cant execute read - pg_query(): Query failed: ERROR:  column &quot;atletas_nombre&quot; does not exist
LINE 1: ...etas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_no...
                                                             ^
HINT:  Perhaps you meant to reference the column &quot;tb_atletas.atletas_nombres&quot;. / 0
DEBUG - 2022-12-26 00:32:33 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:32:34 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:32:34 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:32:34 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:32:34 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:32:34 --> Input Class Initialized
INFO - 2022-12-26 00:32:35 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion from tb_paises order by paises_codigo
DEBUG - 2022-12-26 00:32:36 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:32:36 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:32:36 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:32:36 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:32:36 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:32:36 --> Input Class Initialized
INFO - 2022-12-26 00:32:37 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion from tb_paises order by paises_codigo
DEBUG - 2022-12-26 00:41:52 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:41:53 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:41:53 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:41:53 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:41:53 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:41:53 --> Input Class Initialized
INFO - 2022-12-26 00:41:54 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo from tb_atletas order by atletas_codigo
DEBUG - 2022-12-26 00:41:54 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:41:55 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:41:55 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:41:55 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:41:55 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:41:55 --> Input Class Initialized
INFO - 2022-12-26 00:41:56 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion from tb_paises order by paises_codigo
DEBUG - 2022-12-26 00:41:57 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 00:41:57 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 00:41:57 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 00:41:57 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 00:41:57 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 00:41:57 --> Input Class Initialized
INFO - 2022-12-26 00:41:58 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion from tb_paises order by paises_codigo
DEBUG - 2022-12-26 01:04:24 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 01:04:25 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 01:04:25 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 01:04:25 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 01:04:25 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 01:04:25 --> Input Class Initialized
INFO - 2022-12-26 01:04:26 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo from tb_atletas order by atletas_codigo
DEBUG - 2022-12-26 01:04:26 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 01:04:26 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 01:04:26 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 01:04:27 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 01:04:27 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 01:04:27 --> Input Class Initialized
INFO - 2022-12-26 01:04:28 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion from tb_paises order by paises_codigo
DEBUG - 2022-12-26 01:04:29 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2022-12-26 01:04:29 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-26 01:04:29 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-26 01:04:29 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-26 01:04:29 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-26 01:04:29 --> Input Class Initialized
INFO - 2022-12-26 01:04:30 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion from tb_paises order by paises_codigo
