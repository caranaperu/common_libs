<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2023-01-26 04:45:35 --> Cant execute read - pg_query(): Query failed: ERROR:  type &quot;varying&quot; does not exist
LINE 1: select sp_atletas_save_record(cast(NULL as varying),cast(NUL...
                                                   ^ / 0
DEBUG - 2023-01-26 04:45:37 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 04:45:37 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 04:45:37 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 04:45:37 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 04:45:37 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 04:45:37 --> Input Class Initialized
ERROR - 2023-01-26 04:47:13 --> Cant execute read - pg_query(): Query failed: ERROR:  missing FROM-clause entry for table &quot;p&quot;
LINE 20: ...            AND pg_catalog.pg_function_is_visible(p.oid) AND...
                                                              ^ / 0
DEBUG - 2023-01-26 04:48:37 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 04:48:39 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 04:48:39 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 04:48:39 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 04:48:39 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 04:48:39 --> Input Class Initialized
ERROR - 2023-01-26 04:50:53 --> Cant execute read - pg_query(): Query failed: ERROR:  missing FROM-clause entry for table &quot;p&quot;
LINE 20: ...            AND pg_catalog.pg_function_is_visible(p.oid) AND...
                                                              ^ / 0
DEBUG - 2023-01-26 04:51:09 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 04:51:10 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 04:51:10 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 04:51:10 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 04:51:10 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 04:51:10 --> Input Class Initialized
DEBUG - 2023-01-26 04:52:27 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 04:52:28 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 04:52:28 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 04:52:28 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 04:52:28 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 04:52:28 --> Input Class Initialized
DEBUG - 2023-01-26 04:54:29 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 04:54:29 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 04:54:29 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 04:54:29 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 04:54:29 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 04:54:29 --> Input Class Initialized
DEBUG - 2023-01-26 04:55:37 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 04:55:37 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 04:55:37 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 04:55:37 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 04:55:37 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 04:55:37 --> Input Class Initialized
ERROR - 2023-01-26 05:16:14 --> Cant execute read - pg_query(): Query failed: ERROR:  Se requiere el apellido materno del atleta
CONTEXT:  PL/pgSQL function sp_atletas_save_record(character varying,character varying,character varying,character varying,character,character varying,character varying,character varying,date,character varying,character varying,character varying,character varying,character varying,character varying,character varying,numeric,character varying,character varying,boolean,character varying,integer,bit) line 79 at RAISE / 0
DEBUG - 2023-01-26 05:16:17 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 05:16:17 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 05:16:17 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 05:16:17 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 05:16:17 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 05:16:17 --> Input Class Initialized
DEBUG - 2023-01-26 14:00:07 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:00:08 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:00:08 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:00:08 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:00:08 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:00:08 --> Input Class Initialized
ERROR - 2023-01-26 14:00:33 --> Error executing add : 0 - pg_query(): Query failed: ERROR:  duplicate key value violates unique constraint &quot;pk_atletas&quot;
DETAIL:  Key (atletas_codigo)=(CARANA) already exists.
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
PL/pgSQL function sp_atletas_save_record(character varying,character varying,character varying,character varying,character,character varying,character varying,character varying,date,character varying,character varying,character varying,character varying,character varying,character varying,character varying,numeric,character varying,character varying,boolean,character varying,integer,bit) line 146 at SQL statement - ERROR:  duplicate key value violates unique constraint "pk_atletas"
DETAIL:  Key (atletas_codigo)=(CARANA) already exists.
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
DEBUG - 2023-01-26 14:02:01 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:02:02 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:02:02 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:02:02 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:02:02 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:02:02 --> Input Class Initialized
DEBUG - 2023-01-26 14:02:34 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:02:34 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:02:34 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:02:34 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:02:34 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:02:34 --> Input Class Initialized
INFO - 2023-01-26 14:02:35 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises where paises_codigo ilike 'PER%' order by paises_codigo limit 75 offset 0
DEBUG - 2023-01-26 14:03:27 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:03:27 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:03:27 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:03:27 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:03:27 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:03:27 --> Input Class Initialized
INFO - 2023-01-26 14:03:28 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo limit 75 offset 0
DEBUG - 2023-01-26 14:04:00 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:04:01 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:04:01 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:04:01 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:04:01 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:04:01 --> Input Class Initialized
ERROR - 2023-01-26 14:04:55 --> Error executing add : 0 - pg_query(): Query failed: ERROR:  duplicate key value violates unique constraint &quot;pk_atletas&quot;
DETAIL:  Key (atletas_codigo)=(CARANA) already exists.
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
PL/pgSQL function sp_atletas_save_record(character varying,character varying,character varying,character varying,character,character varying,character varying,character varying,date,character varying,character varying,character varying,character varying,character varying,character varying,character varying,numeric,character varying,character varying,boolean,character varying,integer,bit) line 146 at SQL statement - ERROR:  duplicate key value violates unique constraint "pk_atletas"
DETAIL:  Key (atletas_codigo)=(CARANA) already exists.
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
DEBUG - 2023-01-26 14:08:53 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:08:53 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:08:53 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:08:53 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:08:53 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:08:53 --> Input Class Initialized
DEBUG - 2023-01-26 14:10:21 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:10:22 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:10:22 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:10:22 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:10:22 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:10:22 --> Input Class Initialized
INFO - 2023-01-26 14:10:50 --> affected rows add 1
INFO - 2023-01-26 14:11:44 --> select atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_nombre_completo,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin from tb_atletas where atletas_codigo='MARANA'
DEBUG - 2023-01-26 14:14:33 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:14:34 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:14:34 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:14:34 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:14:34 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:14:34 --> Input Class Initialized
ERROR - 2023-01-26 14:15:03 --> Error executing add : 0 - pg_query(): Query failed: ERROR:  duplicate key value violates unique constraint &quot;pk_atletas&quot;
DETAIL:  Key (atletas_codigo)=(MARANA) already exists.
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
PL/pgSQL function sp_atletas_save_record(character varying,character varying,character varying,character varying,character,character varying,character varying,character varying,date,character varying,character varying,character varying,character varying,character varying,character varying,character varying,numeric,character varying,character varying,boolean,character varying,integer,bit) line 146 at SQL statement - ERROR:  duplicate key value violates unique constraint "pk_atletas"
DETAIL:  Key (atletas_codigo)=(MARANA) already exists.
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
DEBUG - 2023-01-26 14:15:16 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:15:16 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:15:16 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:15:16 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:15:16 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:15:16 --> Input Class Initialized
DEBUG - 2023-01-26 14:15:42 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:15:42 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:15:42 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:15:42 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:15:42 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:15:42 --> Input Class Initialized
INFO - 2023-01-26 14:15:44 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises where paises_codigo ilike 'PER%' and paises_descripcion ilike 'Peru%' order by paises_codigo limit 75 offset 0
DEBUG - 2023-01-26 14:15:55 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:15:55 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:15:55 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:15:55 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:15:55 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:15:55 --> Input Class Initialized
INFO - 2023-01-26 14:15:57 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises where paises_codigo ilike 'PER%' and paises_descripcion ilike 'Peru%' order by paises_codigo limit 75 offset 0
DEBUG - 2023-01-26 14:16:14 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:16:14 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:16:14 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:16:14 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:16:14 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:16:14 --> Input Class Initialized
INFO - 2023-01-26 14:16:15 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises where paises_codigo ilike 'PER%' and paises_descripcion ilike 'Peru%' order by paises_codigo limit 75 offset 0
DEBUG - 2023-01-26 14:16:20 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:16:20 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:16:20 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:16:20 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:16:20 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:16:20 --> Input Class Initialized
INFO - 2023-01-26 14:16:21 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises order by paises_codigo limit 75 offset 0
DEBUG - 2023-01-26 14:16:32 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 14:16:32 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 14:16:32 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 14:16:32 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 14:16:32 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 14:16:32 --> Input Class Initialized
INFO - 2023-01-26 14:16:33 --> select paises_codigo,paises_descripcion,paises_entidad,paises_use_apm,paises_use_docid,regiones_codigo,activo,usuario,fecha_creacion,usuario_mod,fecha_modificacion,xmin from tb_paises where paises_codigo ilike 'PER%' order by paises_codigo limit 75 offset 0
DEBUG - 2023-01-26 19:15:06 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-26 19:15:06 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-26 19:15:06 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-26 19:15:06 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-26 19:15:06 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-26 19:15:06 --> Input Class Initialized
