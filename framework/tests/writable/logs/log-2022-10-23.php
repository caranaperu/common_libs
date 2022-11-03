<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2022-10-23 05:49:06 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-23 04:49:06 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-23 04:49:06 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-23 04:49:06 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-23 04:49:06 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-23 04:49:06 --> Input Class Initialized
INFO - 2022-10-23 04:49:06 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-23 04:49:06 --> 
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
Line: 457 
Line: index 

File: /var/www/common/framework/tests/test_services.php 
Line: 36 
Line: execute_request 

**********************************************************
DEBUG - 2022-10-23 06:00:52 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-23 05:00:52 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-23 05:00:52 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-23 05:00:52 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-23 05:00:52 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-23 05:00:52 --> Input Class Initialized
ERROR - 2022-10-23 05:00:52 --> 
The controller [ test_common.php ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 453 
Line: service 

File: /var/www/common/framework/tests/test_common.php 
Line: 423 
Line: execute_request 

**********************************************************
ERROR - 2022-10-23 05:00:52 --> 
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
DEBUG - 2022-10-23 06:00:54 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-23 05:00:54 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-23 05:00:54 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-23 05:00:54 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-23 05:00:54 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-23 05:00:54 --> Input Class Initialized
ERROR - 2022-10-23 05:00:54 --> 
The controller [ test_common.php ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 453 
Line: service 

File: /var/www/common/framework/tests/test_common.php 
Line: 423 
Line: execute_request 

**********************************************************
ERROR - 2022-10-23 05:00:54 --> 
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
DEBUG - 2022-10-23 06:01:10 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-23 05:01:10 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-23 05:01:10 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-23 05:01:10 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-23 05:01:10 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-23 05:01:10 --> Input Class Initialized
ERROR - 2022-10-23 05:01:10 --> 
The controller [ test_common.php ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 453 
Line: service 

File: /var/www/common/framework/tests/test_common.php 
Line: 423 
Line: execute_request 

**********************************************************
ERROR - 2022-10-23 05:01:10 --> 
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
DEBUG - 2022-10-23 06:01:45 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-23 05:01:47 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-23 05:01:47 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-23 05:02:41 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-23 05:02:41 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-23 05:02:41 --> Input Class Initialized
ERROR - 2022-10-23 05:02:45 --> 
The controller [ test_common.php ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 453 
Line: service 

File: /var/www/common/framework/tests/test_common.php 
Line: 423 
Line: execute_request 

**********************************************************
ERROR - 2022-10-23 05:02:46 --> 
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
DEBUG - 2022-10-23 06:05:34 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-23 05:05:45 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-23 05:05:45 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-23 05:06:12 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-23 05:06:14 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-23 05:06:14 --> Input Class Initialized
ERROR - 2022-10-23 05:06:44 --> 
The controller [ defaultController ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 453 
Line: service 

File: /var/www/common/framework/tests/test_common.php 
Line: 423 
Line: execute_request 

**********************************************************
ERROR - 2022-10-23 05:07:48 --> 
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
DEBUG - 2022-10-23 06:09:01 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-23 05:10:02 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-23 05:10:02 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-23 05:10:14 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-23 05:10:18 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-23 05:10:18 --> Input Class Initialized
ERROR - 2022-10-23 05:11:38 --> 
ini_set(): Headers already sent. You cannot change the session module's ini settings at this time
in /var/www/common/framework/tests/handlers/customFileHandler.php on line 80.
File: /var/www/common/framework/tests/handlers/customFileHandler.php 
Line: 80 
Line: ini_set 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 539 
Line: __construct 

File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 90 
Line: _get_session 

File: /var/www/common/framework/core/FLC.php 
Line: 372 
Line: service 

File: /var/www/common/framework/tests/controllers/my_controller.php 
Line: 27 
Line: session 

File: /var/www/common/framework/core/FLC.php 
Line: 457 
Line: index 

File: /var/www/common/framework/tests/test_common.php 
Line: 423 
Line: execute_request 

**********************************************************
ERROR - 2022-10-23 05:11:38 --> 
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
DEBUG - 2022-10-23 06:12:57 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-23 05:12:59 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-23 05:12:59 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-23 05:13:09 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-23 05:13:10 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-23 05:13:10 --> Input Class Initialized
ERROR - 2022-10-23 05:13:25 --> 
The controller [ defaultController ] doesnt exist, terminal error - SL0001
in /var/www/common/framework/core/flcServiceLocator.php on line 116.
File: /var/www/common/framework/core/flcServiceLocator.php 
Line: 73 
Line: _get_controller 

File: /var/www/common/framework/core/FLC.php 
Line: 453 
Line: service 

File: /var/www/common/framework/tests/test_common.php 
Line: 423 
Line: execute_request 

**********************************************************
ERROR - 2022-10-23 05:13:46 --> 
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
