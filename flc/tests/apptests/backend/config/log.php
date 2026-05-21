<?php

/*
| -------------------------------------------------------------------
| LOG SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to manipulate the log file.
||
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
| log_threshold
| -------------
| You can enable error logging by setting a threshold over zero. The
| threshold determines what gets logged. Threshold options are:
|
|	0 = Disables logging, Error logging TURNED OFF
|	1 = Error Messages (including PHP errors)
|	2 = Debug Messages
|	3 = Informational Messages
|	4 = All Messages
|
| You can also pass an array with threshold levels to show individual error types
|
| 	array(2) = Debug Messages, without Error Messages
|
| For a live site you'll usually only enable Errors (1) to be logged otherwise
| your log files will fill up very fast.
|
| log_path
| --------
| Leave this BLANK unless you would like to set something other than the default
| application/logs/ directory. Use a full server path with trailing slash.
|
| log_file_extension
| -------------------
| The default filename extension for log files.
| Note: Leaving it blank will default to 'log'.
|
| log_file_permissions
| --------------------
| The file system permissions to be applied on newly created log files.
|
| IMPORTANT: This MUST be an integer (no quotes) and you MUST use octal
|            integer notation (i.e. 0700, 0644, etc.)
|
| log_date_format
| ---------------
| Each item that is logged has an associated date. You can use PHP date
| codes to set your own date formatting
|
| log_max_retention
| -----------------
| The logs willl be rotated , when log_max_retention is reached the older ones
| will be deleted , is zero never delete.
| By default will retain 30 days.
|
*/

$log_config = [
    'log_threshold' => 4,
    'log_path' => '/var/www/common/flc/tests/writable/logs/',
    'log_file_extension' => '',
    'log_file_permissions' => 0644,
    'log_date_format' => 'Y-m-d H:i:s',
    'log_max_retention' => 30
];

