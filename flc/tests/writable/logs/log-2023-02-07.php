<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2023-02-07 01:04:15 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-02-07 01:04:16 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-02-07 01:04:16 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-02-07 01:04:16 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-02-07 01:04:16 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-02-07 01:04:16 --> Input Class Initialized
INFO - 2023-02-07 01:04:18 --> delete from tb_atletas where atletas_codigo='07197388'
INFO - 2023-02-07 01:04:18 --> affected rows delete 1
DEBUG - 2023-02-07 01:04:34 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-02-07 01:04:34 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-02-07 01:04:34 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-02-07 01:04:34 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-02-07 01:04:34 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-02-07 01:04:34 --> Input Class Initialized
DEBUG - 2023-02-07 01:06:47 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-02-07 01:06:47 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-02-07 01:06:47 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-02-07 01:06:47 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-02-07 01:06:47 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-02-07 01:06:47 --> Input Class Initialized
INFO - 2023-02-07 01:06:48 --> delete from tb_atletas where atletas_codigo='GGGGG'
INFO - 2023-02-07 01:06:48 --> affected rows delete 1
DEBUG - 2023-02-07 01:07:18 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-02-07 01:07:18 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-02-07 01:07:18 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-02-07 01:07:18 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-02-07 01:07:18 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-02-07 01:07:18 --> Input Class Initialized
INFO - 2023-02-07 01:07:18 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises where paises_codigo ilike 'PER%' order by paises_codigo limit 75 offset 0
DEBUG - 2023-02-07 01:07:51 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-02-07 01:07:51 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-02-07 01:07:51 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-02-07 01:07:52 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-02-07 01:07:52 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-02-07 01:07:52 --> Input Class Initialized
INFO - 2023-02-07 01:07:52 --> delete from tb_atletas where atletas_codigo='DONLYP'
ERROR - 2023-02-07 01:07:52 --> Error executing delete : 0 - pg_query(): Query failed: ERROR:  update or delete on table &quot;tb_atletas&quot; violates foreign key constraint &quot;fk_atletas_resultados_atletas_codigo&quot; on table &quot;tb_atletas_resultados&quot;
DETAIL:  Key (atletas_codigo)=(DONLYP) is still referenced from table &quot;tb_atletas_resultados&quot;. - ERROR:  update or delete on table "tb_atletas" violates foreign key constraint "fk_atletas_resultados_atletas_codigo" on table "tb_atletas_resultados"
DETAIL:  Key (atletas_codigo)=(DONLYP) is still referenced from table "tb_atletas_resultados".(0)
