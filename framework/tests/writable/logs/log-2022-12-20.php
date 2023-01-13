<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2022-12-20 13:12:35 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:12:47 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:12:47 --> flcUtf8->_construct - Utf8 Class Initialized
ERROR - 2022-12-20 13:13:01 --> 
pg_connect(): Unable to connect to PostgreSQL server: connection to server at &quot;localhost&quot; (127.0.0.1), port 5432 failed: Connection refused
	Is the server running on that host and accepting TCP/IP connections?
in /var/www/common/framework/database/driver/postgres/flcPostgresDriver.php on line 238.
File: /var/www/common/framework/database/driver/postgres/flcPostgresDriver.php 
Line: 238 
Line: pg_connect 

File: /var/www/common/framework/database/driver/flcDriver.php 
Line: 395 
Line: _open 

File: /var/www/common/framework/database/driver/postgres/flcPostgresDriver.php 
Line: 265 
Line: open 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 399 
Line: connect 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 82 
Line: _load_database 

File: /var/www/common/framework/core/FLC.php 
Line: 435 
Line: service 

File: /var/www/common/framework/tests/test_services.php 
Line: 35 
Line: execute_request 

**********************************************************
INFO - 2022-12-20 13:13:01 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:13:02 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:13:02 --> Input Class Initialized
DEBUG - 2022-12-20 13:17:30 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:17:31 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:17:31 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:17:35 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:17:35 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:17:35 --> Input Class Initialized
ERROR - 2022-12-20 13:17:41 --> 
The controller [ defaultController ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 455 
Line: service 

File: /var/www/common/framework/tests/test_services.php 
Line: 35 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:41:07 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:41:16 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:41:16 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:42:15 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:42:15 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:42:15 --> Input Class Initialized
ERROR - 2022-12-20 13:42:17 --> 
The controller [ defaultController ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 455 
Line: service 

File: /var/www/common/framework/tests/test_services.php 
Line: 35 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:42:37 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:42:37 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:42:37 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:42:38 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:42:38 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:42:38 --> Input Class Initialized
ERROR - 2022-12-20 13:42:40 --> 
The controller [ test_services ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 455 
Line: service 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:43:28 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:43:29 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:43:29 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:43:29 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:43:29 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:43:29 --> Input Class Initialized
ERROR - 2022-12-20 13:43:28 --> 
The controller [ defaultController ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 455 
Line: service 

File: /var/www/common/framework/tests/test_common.php 
Line: 424 
Line: execute_request 

**********************************************************
ERROR - 2022-12-20 13:43:29 --> 
Uncaught ErrorException: Cannot modify header information - headers already sent by (output started at /var/www/common/framework/tests/test_common.php:171) in /var/www/common/framework/core/flcMessageTrait.php:431
Stack trace:
#0 [internal function]: framework\utils\flcErrorHandlers->_error_handler()
#1 /var/www/common/framework/core/flcMessageTrait.php(431): header()
#2 /var/www/common/framework/utils/flcErrorHandlers.php(81): framework\core\flcResponse->set_status_header()
#3 [internal function]: framework\utils\flcErrorHandlers->_exception_handler()
#4 {main}
  thrown
in /var/www/common/framework/core/flcMessageTrait.php on line 431.
**********************************************************
DEBUG - 2022-12-20 13:44:22 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:44:22 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:44:22 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:44:23 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:44:23 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:44:23 --> Input Class Initialized
ERROR - 2022-12-20 13:44:27 --> 
Class 'framework\core\accessor\core\model\core\session\handler\flcBaseHandler' not found
in /var/www/common/framework/tests/handlers/customFileHandler.php on line 29.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 527 
Line: include_once 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 90 
Line: _get_session 

File: /var/www/common/framework/core/FLC.php 
Line: 372 
Line: service 

File: /var/www/common/framework/tests/controllers/my_controller.php 
Line: 26 
Line: session 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:45:22 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:45:22 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:45:22 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:45:22 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:45:22 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:45:22 --> Input Class Initialized
ERROR - 2022-12-20 13:45:24 --> 
Class 'framework\core\accessor\core\model\core\session\handler\flcBaseHandler' not found
in /var/www/common/framework/tests/handlers/customFileHandler.php on line 29.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 527 
Line: include_once 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 90 
Line: _get_session 

File: /var/www/common/framework/core/FLC.php 
Line: 372 
Line: service 

File: /var/www/common/framework/tests/controllers/my_controller.php 
Line: 26 
Line: session 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:46:09 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:46:09 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:46:09 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:46:09 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:46:09 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:46:09 --> Input Class Initialized
ERROR - 2022-12-20 13:46:11 --> 
Cant load the user session handler [ framework\tests\handlers\customFileHandler ] - SL00026
in /var/www/common/framework/core/flcServiceLocator.php on line 532.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 90 
Line: _get_session 

File: /var/www/common/framework/core/FLC.php 
Line: 372 
Line: service 

File: /var/www/common/framework/tests/controllers/my_controller.php 
Line: 26 
Line: session 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:47:41 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:47:42 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:47:42 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:47:43 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:47:43 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:47:43 --> Input Class Initialized
INFO - 2022-12-20 13:47:47 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-12-20 13:47:48 --> 
Undefined variable: va444r1ww
in /var/www/common/framework/tests/views/my_testview.php on line 3.
File: /var/www/common/framework/tests/views/my_testview.php 
Line: 3 
Line: _error_handler 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 265 
Line: include 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 77 
Line: _get_view 

File: /var/www/common/framework/core/FLC.php 
Line: 359 
Line: service 

File: /var/www/common/framework/tests/controllers/my_controller.php 
Line: 36 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:50:34 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:50:34 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:50:34 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:50:34 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:50:34 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:50:34 --> Input Class Initialized
INFO - 2022-12-20 13:50:34 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-12-20 13:50:34 --> 
Undefined variable: var1ww
in /var/www/common/framework/tests/views/my_testview.php on line 3.
File: /var/www/common/framework/tests/views/my_testview.php 
Line: 3 
Line: _error_handler 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 265 
Line: include 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 77 
Line: _get_view 

File: /var/www/common/framework/core/FLC.php 
Line: 359 
Line: service 

File: /var/www/common/framework/tests/controllers/my_controller.php 
Line: 36 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:50:47 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:50:47 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:50:47 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:50:47 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:50:47 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:50:47 --> Input Class Initialized
INFO - 2022-12-20 13:50:47 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-12-20 13:50:47 --> 
Undefined variable: var1
in /var/www/common/framework/tests/views/my_testview.php on line 3.
File: /var/www/common/framework/tests/views/my_testview.php 
Line: 3 
Line: _error_handler 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 265 
Line: include 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 77 
Line: _get_view 

File: /var/www/common/framework/core/FLC.php 
Line: 359 
Line: service 

File: /var/www/common/framework/tests/controllers/my_controller.php 
Line: 36 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-12-20 13:51:55 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-12-20 13:51:55 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-12-20 13:51:55 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-12-20 13:51:55 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-12-20 13:51:55 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-12-20 13:51:55 --> Input Class Initialized
INFO - 2022-12-20 13:51:55 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-12-20 13:51:55 --> 
Undefined variable: var1
in /var/www/common/framework/tests/views/my_testview.php on line 3.
File: /var/www/common/framework/tests/views/my_testview.php 
Line: 3 
Line: _error_handler 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 265 
Line: include 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 77 
Line: _get_view 

File: /var/www/common/framework/core/FLC.php 
Line: 359 
Line: service 

File: /var/www/common/framework/tests/controllers/my_controller.php 
Line: 37 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
