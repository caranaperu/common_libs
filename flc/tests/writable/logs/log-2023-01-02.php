<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2023-01-02 00:52:36 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-02 00:52:36 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-02 00:52:36 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-02 00:52:36 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-02 00:52:36 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-02 00:52:36 --> Input Class Initialized
DEBUG - 2023-01-02 00:54:23 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-02 00:54:23 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-02 00:54:23 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-02 00:54:23 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-02 00:54:23 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-02 00:54:23 --> Input Class Initialized
INFO - 2023-01-02 00:54:23 --> INSERT INTO tb_atletas (atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion,xmin) VALUES ('XXXX','aaaaaaa','xxxxxxxx','ccccccccccccc','M','08809086','','CHI','1960-12-29','null','null','','ssssss sdf sdfsfdsdfdsfdsfdsf','','L','M',34,'US','null','0','1',NULL,NULL,NULL)
ERROR - 2023-01-02 00:54:23 --> Cant execute read - pg_query(): Query failed: ERROR:  column &quot;xmin&quot; of relation &quot;tb_atletas&quot; does not exist
LINE 1: ...o,atletas_protected,activo,usuario,fecha_creacion,xmin) VALU...
                                                             ^ / 0
DEBUG - 2023-01-02 01:24:16 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-02 01:24:16 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-02 01:24:16 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-02 01:24:16 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-02 01:24:16 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-02 01:24:16 --> Input Class Initialized
DEBUG - 2023-01-02 01:25:00 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-02 01:25:00 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-02 01:25:00 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-02 01:25:00 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-02 01:25:00 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-02 01:25:00 --> Input Class Initialized
INFO - 2023-01-02 01:25:00 --> INSERT INTO tb_atletas (atletas_codigo,atletas_ap_paterno,atletas_ap_materno,atletas_nombres,atletas_sexo,atletas_nro_documento,atletas_nro_pasaporte,paises_codigo,atletas_fecha_nacimiento,atletas_telefono_casa,atletas_telefono_celular,atletas_email,atletas_direccion,atletas_observaciones,atletas_talla_ropa_buzo,atletas_talla_ropa_poloshort,atletas_talla_zapatillas,atletas_norma_zapatillas,atletas_url_foto,atletas_protected,activo,usuario,fecha_creacion) VALUES ('555555','55ttttttt','mmmmmmmm','nnnnnnn','M','08809086','null','BRA','1961-12-29','3232333333','null','null','ddddddddddddd','null','S','M',null,'US','null','0','1',NULL,NULL)
ERROR - 2023-01-02 01:25:00 --> Cant execute read - pg_query(): Query failed: ERROR:  null value in column &quot;atletas_nombre_completo&quot; of relation &quot;tb_atletas&quot; violates not-null constraint
DETAIL:  Failing row contains (555555, 55ttttttt, mmmmmmmm, nnnnnnn, null, M, 08809086, null, BRA, 1961-12-29, 3232333333, null, null, ddddddddddddd, null, S, M, null, US, null, t, postgres, 2023-01-02 01:24:58.912134, null, null, f). / 0
DEBUG - 2023-01-02 04:23:36 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-02 04:23:36 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-02 04:23:36 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-02 04:23:36 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-02 04:23:36 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-02 04:23:36 --> Input Class Initialized
DEBUG - 2023-01-02 04:25:26 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-02 04:25:26 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-02 04:25:26 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-02 04:25:26 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-02 04:25:26 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-02 04:25:26 --> Input Class Initialized
ERROR - 2023-01-02 04:28:07 --> Cant execute read - pg_query(): Query failed: ERROR:  syntax error at or near &quot;XXXXX&quot;
LINE 1: call sp_atletas_save_record(''XXXXX'',''carlos'',''arana'','...
                                      ^ / 0
DEBUG - 2023-01-02 04:32:20 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-02 04:32:21 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-02 04:32:21 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-02 04:32:21 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-02 04:32:21 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-02 04:32:21 --> Input Class Initialized
DEBUG - 2023-01-02 04:40:55 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/apptests/backend/config/config.php
DEBUG - 2023-01-02 04:40:56 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2023-01-02 04:40:56 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2023-01-02 04:40:56 --> flcResponse->_construct - Response class initialized
DEBUG - 2023-01-02 04:40:56 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2023-01-02 04:40:56 --> Input Class Initialized
ERROR - 2023-01-02 04:40:57 --> Cant execute read - pg_query(): Query failed: ERROR:  function sp_atletas_save_record(unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, unknown, boolean, unknown, unknown, unknown, boolean) does not exist
LINE 1: select sp_atletas_save_record('SSSS','dddddd','dddsdsdsdsd',...
               ^
HINT:  No function matches the given name and argument types. You might need to add explicit type casts. / 0
