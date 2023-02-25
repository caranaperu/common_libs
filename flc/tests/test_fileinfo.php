<?php

$application_folder = '/var/www/common/flc/deploy/apppath_directory';
$system_path = '/var/www/common/flc';

define('APPPATH', $application_folder.DIRECTORY_SEPARATOR);
// Path to the system directory
define('BASEPATH', $system_path);

/**
 * List of Mime Types
 *
 * This is a list of mime types.  We use it to validate
 * the "allowed types" set by the developer
 *
 * @param string $p_mime_type
 *
 * @return array|string|null
 */
 function mimes_types(string $p_mime_type) {
    global $mimes;

    if (!isset($mime_types) || count($mime_types) == 0) {
        if (is_file(APPPATH.'config/mimes.php')) {
            include(APPPATH.'config/mimes.php');
        } else {
            return null;
        }

        $mime_types = $mimes;
        unset($mimes);
    }

    return (!isset($mime_types[$p_mime_type])) ? null : $mime_types[$p_mime_type];
}

$file_info = finfo_open(FILEINFO_MIME_TYPE );
$ext = finfo_file($file_info, '/home/carlos/docker_otros_configs/jasperserver_createkeys.txt');
echo $ext.PHP_EOL;

$file_info = finfo_open(FILEINFO_MIME_TYPE );
$ext = finfo_file($file_info, '/home/carlos/testext/Dockerfile,gif');
echo $ext.PHP_EOL;

$fileName = '/home/carlos/docker_otros_configs/jasperserver_createkeys.txt';
$ext = pathinfo($fileName, PATHINFO_EXTENSION);

echo $ext.PHP_EOL;


$fileName = '/home/carlos/testext/Dockerfile';
$ext = pathinfo($fileName, PATHINFO_EXTENSION);
$ext = pathinfo($fileName, PATHINFO_EXTENSION);

echo $ext.PHP_EOL;

$fileName = '/home/carlos/testext/search.js';
$ext = pathinfo($fileName, PATHINFO_EXTENSION);

echo $ext.PHP_EOL;

print_r(mimes_types('txt'));

echo 'is_writable'.PHP_EOL;
echo(is_writable('/home/carlos/testext')).PHP_EOL;
