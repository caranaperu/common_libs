<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

DEBUG - 2022-10-22 03:26:47 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-22 02:26:47 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-22 02:26:47 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-22 02:26:47 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-22 02:26:47 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-22 02:26:47 --> Input Class Initialized
INFO - 2022-10-22 02:26:47 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-22 02:26:47 --> 
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
DEBUG - 2022-10-22 03:26:50 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-22 02:26:50 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-22 02:26:50 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-22 02:26:50 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-22 02:26:50 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-22 02:26:50 --> Input Class Initialized
INFO - 2022-10-22 02:26:50 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-22 02:26:50 --> 
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
DEBUG - 2022-10-22 03:56:52 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-22 02:56:52 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-22 02:56:52 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-22 02:56:52 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-22 02:56:52 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-22 02:56:52 --> Input Class Initialized
INFO - 2022-10-22 02:56:52 --> Session: Class initialized using 'customFileHandler' driver.
ERROR - 2022-10-22 02:56:52 --> 
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
DEBUG - 2022-10-22 04:02:35 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-22 03:02:35 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-22 03:02:35 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-22 03:02:35 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-22 03:02:35 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-22 03:02:35 --> Input Class Initialized
ERROR - 2022-10-22 03:02:35 --> 
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
ERROR - 2022-10-22 03:02:35 --> 
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
DEBUG - 2022-10-22 04:02:36 --> flcConfig->load : Config file loaded /var/www/common/framework/tests/config/config.php
DEBUG - 2022-10-22 03:02:36 --> flcUtf8->_construct - UTF-8 Support Enabled
INFO - 2022-10-22 03:02:36 --> flcUtf8->_construct - Utf8 Class Initialized
INFO - 2022-10-22 03:02:36 --> flcResponse->_construct - Response class initialized
DEBUG - 2022-10-22 03:02:36 --> flcRequest->__constructor - Global POST, GET and COOKIE data sanitized
INFO - 2022-10-22 03:02:36 --> Input Class Initialized
ERROR - 2022-10-22 03:02:36 --> 
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
ERROR - 2022-10-22 03:02:36 --> 
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
