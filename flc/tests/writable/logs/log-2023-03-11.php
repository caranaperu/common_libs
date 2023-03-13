<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2023-03-11 04:39:02 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-11 04:39:02 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-11 04:39:02 --> flcUtf8->_construct - Utf8 Class Initialized
DEBUG - 2023-03-11 04:39:02 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-11 04:39:02 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-11 04:39:02 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-11 04:39:02 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-11 04:39:02 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-11 04:39:02 --> Input Class Initialized
INFO - 2023-03-11 04:39:02 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
INFO - 2023-03-11 04:39:02 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-11 04:39:02 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-11 04:39:02 --> Input Class Initialized
ERROR - 2023-03-11 04:39:02 --> Error executing fetch full : 0 - Argument 1 passed to flc\core\accessor\flcDbAccessor::where_clause() must be of the type string, array given, called in /var/www/common/flc/tests/apptests/backend/accessors/atletas_accessor.php on line 33 - (0)
DEBUG - 2023-03-11 04:41:00 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-11 04:41:00 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-11 04:41:00 --> flcUtf8->_construct - Utf8 Class Initialized
DEBUG - 2023-03-11 04:41:00 --> flcConfig->load : Config file loaded /var/www/common/flc/tests/apptests/backend/config/config.php
DEBUG - 2023-03-11 04:41:00 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-03-11 04:41:00 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-03-11 04:41:00 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-11 04:41:00 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-11 04:41:00 --> Input Class Initialized
INFO - 2023-03-11 04:41:00 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo 
INFO - 2023-03-11 04:41:00 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-03-11 04:41:00 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-03-11 04:41:00 --> Input Class Initialized
ERROR - 2023-03-11 04:41:00 --> Error executing fetch full : 0 - pg_query(): Query failed: ERROR:  missing FROM-clause entry for table &quot;tb_atletas&quot;
LINE 1: ...VARYING AS atletas_agno from  tb_atletas) a where tb_atletas...
                                                             ^ - ERROR:  missing FROM-clause entry for table "tb_atletas"
LINE 1: ...VARYING AS atletas_agno from  tb_atletas) a where tb_atletas...
                                                             ^(0)
