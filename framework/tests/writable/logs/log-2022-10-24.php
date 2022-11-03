<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2022-10-24 02:42:23 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 01:42:25 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 01:42:25 --> flcUtf8->_construct - Utf8 Class Initialized
DEBUG - 2022-10-24 02:43:46 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 01:43:48 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 01:43:48 --> flcUtf8->_construct - Utf8 Class Initialized
ERROR - 2022-10-24 01:45:19 --> 
Array to string conversion
in /var/www/common/framework/flcCommon.php on line 261.
File: /var/www/common/framework/flcCommon.php 
Line: 261 
Line: _error_handler 

File: /var/www/common/framework/core/FLC.php 
Line: 438 
Line: uri_parse_params 

File: /var/www/common/framework/tests/test_common.php 
Line: 423 
Line: execute_request 

**********************************************************
INFO - 2022-10-24 01:45:20 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 01:45:20 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 01:45:20 --> Input Class Initialized
ERROR - 2022-10-24 01:45:21 --> 
Uncaught ErrorException: Cannot modify header information - headers already sent by (output started at /var/www/common/framework/tests/test_common.php:170) in /var/www/common/framework/core/flcMessageTrait.php:431
Stack trace:
#0 [internal function]: framework\utils\flcErrorHandlers->_error_handler()
#1 /var/www/common/framework/core/flcMessageTrait.php(431): header()
#2 /var/www/common/framework/utils/flcErrorHandlers.php(80): framework\core\flcResponse->set_status_header()
#3 [internal function]: framework\utils\flcErrorHandlers->_exception_handler()
#4 {main}
  thrown
in /var/www/common/framework/core/flcMessageTrait.php on line 431.
**********************************************************
DEBUG - 2022-10-24 02:45:59 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 01:46:01 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 01:46:01 --> flcUtf8->_construct - Utf8 Class Initialized
DEBUG - 2022-10-24 02:59:11 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 01:59:11 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 01:59:11 --> flcUtf8->_construct - Utf8 Class Initialized
ERROR - 2022-10-24 01:59:11 --> 
Array to string conversion
in /var/www/common/framework/flcCommon.php on line 261.
File: /var/www/common/framework/flcCommon.php 
Line: 261 
Line: _error_handler 

File: /var/www/common/framework/core/FLC.php 
Line: 438 
Line: uri_parse_params 

File: /var/www/common/framework/tests/test_services.php 
Line: 36 
Line: execute_request 

**********************************************************
INFO - 2022-10-24 01:59:12 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 01:59:12 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 01:59:12 --> Input Class Initialized
ERROR - 2022-10-24 01:59:12 --> 
Uncaught ErrorException: Cannot modify header information - headers already sent by (output started at /var/www/common/framework/flcCommon.php:255) in /var/www/common/framework/core/flcMessageTrait.php:431
Stack trace:
#0 [internal function]: framework\utils\flcErrorHandlers->_error_handler()
#1 /var/www/common/framework/core/flcMessageTrait.php(431): header()
#2 /var/www/common/framework/utils/flcErrorHandlers.php(80): framework\core\flcResponse->set_status_header()
#3 [internal function]: framework\utils\flcErrorHandlers->_exception_handler()
#4 {main}
  thrown
in /var/www/common/framework/core/flcMessageTrait.php on line 431.
**********************************************************
DEBUG - 2022-10-24 02:59:34 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 01:59:34 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 01:59:34 --> flcUtf8->_construct - Utf8 Class Initialized
ERROR - 2022-10-24 01:59:35 --> 
Array to string conversion
in /var/www/common/framework/flcCommon.php on line 261.
File: /var/www/common/framework/flcCommon.php 
Line: 261 
Line: _error_handler 

File: /var/www/common/framework/core/FLC.php 
Line: 438 
Line: uri_parse_params 

File: /var/www/common/framework/tests/test_services.php 
Line: 36 
Line: execute_request 

**********************************************************
INFO - 2022-10-24 01:59:35 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 01:59:35 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 01:59:35 --> Input Class Initialized
ERROR - 2022-10-24 01:59:35 --> 
Uncaught ErrorException: Cannot modify header information - headers already sent by (output started at /var/www/common/framework/flcCommon.php:255) in /var/www/common/framework/core/flcMessageTrait.php:431
Stack trace:
#0 [internal function]: framework\utils\flcErrorHandlers->_error_handler()
#1 /var/www/common/framework/core/flcMessageTrait.php(431): header()
#2 /var/www/common/framework/utils/flcErrorHandlers.php(80): framework\core\flcResponse->set_status_header()
#3 [internal function]: framework\utils\flcErrorHandlers->_exception_handler()
#4 {main}
  thrown
in /var/www/common/framework/core/flcMessageTrait.php on line 431.
**********************************************************
DEBUG - 2022-10-24 02:59:53 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 01:59:53 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 01:59:53 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-24 01:59:54 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 01:59:54 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 01:59:54 --> Input Class Initialized
INFO - 2022-10-24 01:59:55 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-24 01:59:55 --> 
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
Line: 37 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 458 
Line: index 

File: /var/www/common/framework/tests/test_services.php 
Line: 36 
Line: execute_request 

**********************************************************
DEBUG - 2022-10-24 03:00:11 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 02:00:11 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 02:00:11 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-24 02:00:12 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 02:00:12 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 02:00:12 --> Input Class Initialized
INFO - 2022-10-24 02:00:13 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-24 02:00:13 --> 
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
Line: 37 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 458 
Line: index 

File: /var/www/common/framework/tests/test_services.php 
Line: 36 
Line: execute_request 

**********************************************************
DEBUG - 2022-10-24 03:39:00 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 02:39:22 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 02:39:22 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-24 02:39:34 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 02:39:36 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 02:39:36 --> Input Class Initialized
INFO - 2022-10-24 02:39:43 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-24 02:39:45 --> 
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
Line: 37 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-10-24 05:10:54 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 04:10:54 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 04:10:54 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-24 04:10:55 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 04:10:55 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 04:10:55 --> Input Class Initialized
INFO - 2022-10-24 04:10:56 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-24 04:10:56 --> 
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
Line: 37 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/test_services.php 
Line: 36 
Line: execute_request 

**********************************************************
DEBUG - 2022-10-24 05:11:13 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 04:11:13 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 04:11:13 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-24 04:11:25 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 04:11:25 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 04:11:25 --> Input Class Initialized
INFO - 2022-10-24 04:11:27 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-24 04:11:27 --> 
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
Line: 37 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
DEBUG - 2022-10-24 05:15:37 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-24 04:15:37 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-24 04:15:37 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-24 04:16:52 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-24 04:16:52 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-24 04:16:52 --> Input Class Initialized
INFO - 2022-10-24 04:16:55 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-24 04:16:56 --> 
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
Line: 37 
Line: view 

File: /var/www/common/framework/core/FLC.php 
Line: 459 
Line: index 

File: /var/www/common/framework/tests/index.php 
Line: 183 
Line: execute_request 

**********************************************************
