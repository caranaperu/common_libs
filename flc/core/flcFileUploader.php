<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Carlos Arana Reategui.
 *
 */

namespace flc\core;

use flc\flcCommon;

/**
 * File loader class , this class allow the load of one file not multiple files ,
 * check mime types allowed , max size , valid upload path and also can encrypt the name.
 */
class flcFileUploader {

    /**
     * The file name require to be saved with a encrypted name?
     * @var bool
     */
    protected bool $encrypt_file_name = false;

    /**
     * Array of file extensions allowed to upload.
     * @var array
     */
    protected array $mime_ext_allowed = [];

    /**
     * The field name that contains the $FIELD descriptor
     * @var string
     */
    protected string $field_name;

    /**
     * The destination upload path.
     * @var string
     */
    protected string $upload_path;

    /**
     * The maximun file size , by default 2048 kilobutes
     * @var int maximun in kilobytes.
     */
    protected int $max_file_size;

    /********************************************************
     * Output holders
     */

    /**
     * The final name of the uploaded file , thats because
     * can be envrypted.
     * @var string
     */
    protected string $file_name_uploaded = '';


    /**
     * The error code or empty string if all went ok.
     * @var string
     */
    protected string $error_code = '';

    /**
     * Constructor.
     *
     * The options key required are :
     * - field_name : the field that contains the $FILE descriptor
     * - mime_ext_allowed : the extension allowed , like ['jpg','gif','pdf']
     * - upload path : the final folder destination on the server
     * - encrypt_file_name : true if required to encrypt the filename (default is false)
     * - max_file_size : the maximun fil sixe allowed to upload (2mb by default)
     *
     * @param array $p_options
     */
    public function __construct(array $p_options) {
        $this->field_name = $p_options['field_name'] ?? '';
        $this->mime_ext_allowed = $p_options['mime_ext_allowed'] ?? [];
        $this->upload_path = $p_options['upload_path'] ?? '';
        $this->encrypt_file_name = $p_options['encrypt_file_name'] ?? false;
        $this->max_file_size = $p_options['max_file_size'] ?? 2048;
        $this->error_code = '';
    }

    // --------------------------------------------------------------------

    /**
     * Then main function to do the upload.
     *
     * @return bool
     */
    public function do_upload(): bool {

        if (!isset($_FILES[$this->field_name]['name'])) {
            $this->error_code = 'UPLOAD_ERR_NO_FILE';

            return false;
        }

        if (!$this->_is_valid_upload_path($this->upload_path)) {
            return false;
        }

        // Can upload the file?.
        if (!is_uploaded_file($_FILES[$this->field_name]['tmp_name'])) {
            $error = $_FILES[$this->field_name]['error'] ?? 4;

            switch ($error) {
                case UPLOAD_ERR_INI_SIZE:
                    $this->error_code = 'UPLOAD_ERR_INI_SIZE';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $this->error_code = 'UPLOAD_ERR_FORM_SIZE';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $this->error_code = 'UPLOAD_ERR_PARTIAL';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $this->error_code = 'UPLOAD_ERR_NO_FILE';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $this->error_code = 'UPLOAD_ERR_NO_TMP_DIR';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $this->error_code = 'UPLOAD_ERR_CANT_WRITE';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $this->error_code = 'UPLOAD_ERR_EXTENSION';
                    break;
                default :
                    // UPLOAD_ERR_NO_FILE
                    $this->error_code = 'UPLOAD_ERR_NO_FILE';
                    break;
            }

            return false;
        }


        $file_path = $_FILES[$this->field_name]['tmp_name'];
        $this->file_name_uploaded = $this->_clean_file_name($_FILES[$this->field_name]['name']);
        $file_size = filesize($file_path);
        $file_info = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($file_info, $file_path);
        $file_ext = pathinfo($this->file_name_uploaded, PATHINFO_EXTENSION);

        if ($this->encrypt_file_name) {
            $this->file_name_uploaded = $this->_encrypt_file_name($this->upload_path, $file_ext);
        }

        if (!$this->_check_max_size($file_size)) {
            $this->error_code = 'C_UPLOAD_ERR_MAX_SIZE';

            return false;
        }

        if (!$this->_is_writable_dir($this->upload_path)) {
            return false;
        }


        if (!$this->_verify_mime_type_is_allowed($file_type, $file_ext)) {
            $this->error_code = 'C_UPLOAD_MIME_NOT_ALLOWED';

            return false;
        }

        /**
         * Because some configurations of the server move_upload_file isnot
         * reliable , first try a normal copy , if fails try move_uploaded_file.
         */
        if (!@copy($file_path, $this->upload_path.DIRECTORY_SEPARATOR.$this->file_name_uploaded)) {
            if (!@move_uploaded_file($file_path, $this->upload_path.DIRECTORY_SEPARATOR.$this->file_name_uploaded)) {
                $this->error_code = 'C_UPLOAD_DESTINATION_ERROR';

                return false;
            }
        } else {
            // delete tmp file after copy, not requiered on move.
            @unlink($file_path);
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Check if the uploaded file size exceed the max allowed by the class
     * or the server.
     *
     * The maximun allowed will be the min between the max_file_size and the
     * server upload_max_filesize (php.ini).
     *
     * @param int $p_file_size the size of the file to upload.
     *
     * @return bool true if the size is correct.
     */
    private function _check_max_size(int $p_file_size): bool {
        // we should not exceed php.ini max file size
        $ini_maxsize = ini_get('upload_max_filesize');
        if (!is_numeric($ini_maxsize)) {
            if (strpos($ini_maxsize, 'M') !== false) {
                $ini_maxsize = intval($ini_maxsize) * 1024 * 1024;
            } elseif (strpos($ini_maxsize, 'K') !== false) {
                $ini_maxsize = intval($ini_maxsize) * 1024;
            } elseif (strpos($ini_maxsize, 'G') !== false) {
                $ini_maxsize = intval($ini_maxsize) * 1024 * 1024 * 1024;
            }
        }

        // If is zero accpet the maximun size
        if ($this->max_file_size == 0) {
            if ($p_file_size > $ini_maxsize) {
                return false;
            }
        }

        $req_max_file_size = $this->max_file_size * 1024;
        $ini_maxsize = min($ini_maxsize, $req_max_file_size);

        if ($p_file_size > $ini_maxsize) {
            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Remove dangerous characters in the filename and replace for '_'.
     *
     * @param string $p_file_name the filename to process.
     *
     * @return string with the processed filename.
     */
    private function _clean_file_name(string $p_file_name): string {
        $bad_chars = [
            "<!--",
            "-->",
            "'",
            "<",
            ">",
            '"',
            '&',
            '$',
            '=',
            ';',
            '?',
            '/',
            "%20",
            "%22",
            "%3c",        // <
            "%253c",    // <
            "%3e",        // >
            "%0e",        // >
            "%28",        // (
            "%29",        // )
            "%2528",    // (
            "%26",        // &
            "%24",        // $
            "%3f",        // ?
            "%3b",        // ;
            "%3d"        // =
        ];

        return str_replace($bad_chars, '_', $p_file_name);
    }

    // --------------------------------------------------------------------

    private function _encrypt_file_name(string $p_dest_path, string $p_file_ext): string {
        $i = 0;
        do {
            $dest_filename = substr(md5(uniqid(rand(), true)), 0, 8)."_$i.$p_file_ext";
        } while (file_exists($p_dest_path.$dest_filename));

        return $dest_filename;
    }

    // --------------------------------------------------------------------

    /**
     * Verify if the destination upload folder is valid.
     * Check if a real directory and if its writable.
     *
     * @return bool true if valid.
     */
    private function _is_valid_upload_path(string $p_upload_path): bool {
        if (trim($p_upload_path) == '') {
            $this->error_code = 'C_UPLOAD_NO_FILEPATH';

            return false;
        }

        // get the real path normalized for unix style.
        if (@realpath($p_upload_path) !== false) {
            $this->upload_path = str_replace("\\", "/", realpath($p_upload_path));
        }

        // is a direcotry
        if (!@is_dir($p_upload_path)) {
            $this->error_code = 'C_UPLOAD_NO_FILEPATH';

            return false;
        }

        // can be written a file on the directory ?
        if (!$this->_is_writable_dir($this->upload_path)) {
            $this->error_code = 'C_UPLOAD_FILEPATH_NOT_WRITABLE';

            return false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Verify if the upload directory is writable , if windows and not linux we try to write a file
     * inside and check if can be written, becuase is_wirtable function inot reliable under some
     * windows conditions.
     *
     * Some libraries check for linux safe mode , really is stupid nobody will use in production
     * a server in safe mode.
     *
     * @param string $p_directory the upload directory
     *
     * @return bool
     */
    private function _is_writable_dir(string $p_directory): bool {
        // If we're on a Unix server with safe_mode off we call is_writable
        if (DIRECTORY_SEPARATOR == '/') {
            return is_writable($p_directory);
        }

        // For windows servers and safe_mode "on" installations we'll actually
        // write a file then read it.  Bah...
        if (is_dir($p_directory)) {
            $file = rtrim($p_directory, '/').'/'.md5(mt_rand(1, 100).mt_rand(1, 100));

            if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === false) {
                return false;
            }

            fclose($fp);
            @chmod($file, DIR_WRITE_MODE);
            @unlink($file);
            fclose($fp);

            return true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Verify if the extension/myme type conbination is allowed and valid.
     * Before calling this method the mime type is obtained using finfo_pen/f_info_file
     * not the $FILES as usual , this guarantee that a real mime type is obtained.
     *
     * Thats why this method check the combination as valid and disallow that a sender
     * send a file of one type based on a modified extension.
     *
     * @param string $p_mime_type ie: text/html
     * @param string $p_file_ext the extesion like gif,jpg,pdf, etc
     *
     * @return bool true if the extension/myme type conbination is allowed and valid.
     */
    private function _verify_mime_type_is_allowed(string $p_mime_type, string $p_file_ext): bool {
        // 0 length means all types allowed
        if (count($this->mime_ext_allowed) == 0) {
            return true;
        }

        if (!in_array($p_file_ext, $this->mime_ext_allowed)) {
            return false;
        }

        $mime_type = $this->_mimes_types($p_file_ext);

        if ($mime_type !== null) {
            if (is_array($mime_type)) {
                if (!in_array($p_mime_type, $mime_type)) {
                    return false;
                }

            } else {
                if ($p_mime_type != $mime_type) {
                    return false;
                }
            }

        } else {
            return false;
        }

        return true;

    }

    // --------------------------------------------------------------------

    /**
     * List of Mime Types
     *
     * This is a list of mime types.  We use it to validate
     * the "allowed types" set by the developer
     *
     * @param string $p_ext the file extension
     *
     * @return array|null
     */
    private function _mimes_types(string $p_ext): ?array {

        $mimes = flcCommon::get_mimes();

        if (count($mimes) > 0) {
            return $mimes[$p_ext] ?? null;
        }

        return null;
    }

    // --------------------------------------------------------------------

    /**
     * Return the final name with or without encryption. (no path)
     *
     * @return string the filename
     */
    public function get_uploaded_filename(): string {
        return $this->file_name_uploaded;
    }

    // --------------------------------------------------------------------

    /**
     * Return the error code.
     *
     * @return string the error code as string or '' if no errors.
     */
    public function get_error_code(): string {
        return $this->error_code;
    }

}