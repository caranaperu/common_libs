<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Inspired in codeigniter , all kudos for his authors
 *
 * @author Carlos Arana Reategui.
 *
 */

namespace framework\core;

use DateTime;
use Exception;
use framework\utils\flcStrUtils;


/**
 * Logging Class
 *
 */
class flcLog {

    /**
     * Path to save log files
     *
     * @var string
     */
    protected string $_log_path;

    /**
     * File permissions
     *
     * @var    int
     */
    protected int $_file_permissions = 0644;

    /**
     * Level of logging
     *
     * @var int
     */
    protected int $_threshold = 1;

    /**
     * Array of threshold levels to log
     *
     * @var array
     */
    protected array $_threshold_array = [];

    /**
     * Format of timestamp for log files
     *
     * @var string
     */
    protected string $_date_fmt = 'Y-m-d H:i:s';

    /**
     * Filename extension
     *
     * @var    string
     */
    protected string $_file_ext;

    /**
     * Whether or not the logger can write to the log files
     *
     * @var bool
     */
    protected bool $_enabled = true;

    /**
     * Predefined logging levels
     *
     * @var array
     */
    protected array $_levels = ['ERROR' => 1, 'DEBUG' => 2, 'INFO' => 3, 'ALL' => 4];

    /**
     * Predefined max files retention.
     *
     * @var int
     */
    protected int $_log_max_retention = 30;

    /**
     * mbstring.func_override flag
     *
     * @var    bool
     */
    protected static bool $func_override;

    // --------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @return    void
     */
    public function __construct(array $p_log_options) {

        isset(self::$func_override) or self::$func_override = (extension_loaded('mbstring') && ini_get('mbstring.func_override'));

        $this->_log_path = ($p_log_options['log_path'] !== '') ? $p_log_options['log_path'] : APPPATH.'logs/';
        $this->_file_ext = (isset($p_log_options['log_file_extension']) && $p_log_options['log_file_extension'] !== '') ? ltrim($p_log_options['log_file_extension'], '.') : 'php';

        file_exists($this->_log_path) or mkdir($this->_log_path, 0755, true);

        if (!is_dir($this->_log_path) or !is_writable($this->_log_path)) {
            $this->_enabled = false;
        }

        if (is_numeric($p_log_options['log_threshold'])) {
            $this->_threshold = (int)$p_log_options['log_threshold'];
        } elseif (is_array($p_log_options['log_threshold'])) {
            $this->_threshold = 0;
            $this->_threshold_array = array_flip($p_log_options['log_threshold']);
        }

        if (!empty($p_log_options['log_date_format'])) {
            $this->_date_fmt = $p_log_options['log_date_format'];
        }

        if (!empty($p_log_options['log_file_permissions']) && is_int($p_log_options['log_file_permissions'])) {
            $this->_file_permissions = $p_log_options['log_file_permissions'];
        }

        if (!empty($p_log_options['log_max_retention']) && is_int($p_log_options['log_max_retention'])) {
            $this->_log_max_retention = $p_log_options['log_max_retention'];
        }

    }

    // --------------------------------------------------------------------

    /**
     * Execute the log rotation of the files , only left the indicated by log_max_retention config
     * item.
     *
     * This helper method need to be called at least one time by day.
     *
     * @return void
     */
    public function do_log_rotate() {
        $documentspath = $this->_log_path;

        $dir = opendir($documentspath);

        $tmp = [];
        while ($documents = readdir($dir)) {
            $fullpath = $documentspath.'/'.$documents;
            if ($documents != '.' and $documents != '..' && !is_dir($fullpath)) {

                // append the file name , is required? well for one log file per day
                // i think no .
                // When this works? when a process create a ton of files in the same second
                // its not the case , just in cas. :)
                $ctime = filectime($fullpath).','.$documents;
                $tmp[$ctime] = $documents;
            }
        }
        closedir($dir);

        // Only try to delete if the number of files found exceed the log max retention.
        if (count($tmp) > $this->_log_max_retention) {
            // reverser order
            krsort($tmp);

            $tmp = array_slice($tmp, $this->_log_max_retention);
            // delete the oldest one , left only _log_max_retention files.
            foreach ($tmp as $key => $file) {
                // unconditionally delete a file , if one file cant be deleted
                // because for example its open by another process or some admin
                // its reading , in the next cycle will be deleted.
                unlink($documentspath.'/'.$file);

            }

        }


    }

    // --------------------------------------------------------------------

    /**
     * Write Log File
     *
     * Generally this function will be called using the global log_message() function
     *
     * @param string $p_level The error level: 'error', 'debug' or 'info'
     * @param string $p_msg The error message
     *
     * @return    bool
     * @throws Exception
     */
    public function write_log(string $p_level, string $p_msg): bool {
        $result = false;

        if ($this->_enabled === false) {
            return false;
        }

        $p_level = strtoupper($p_level);

        if ((!isset($this->_levels[$p_level]) or ($this->_levels[$p_level] > $this->_threshold)) && !isset($this->_threshold_array[$this->_levels[$p_level]])) {
            return false;
        }

        $filepath = $this->_log_path.'log-'.date('Y-m-d').'.'.$this->_file_ext;
        $message = '';

        if (!file_exists($filepath)) {
            $newfile = true;
            // Only add protection to php files
            if ($this->_file_ext === 'php') {
                $message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
            }
        }

        if (!$fp = @fopen($filepath, 'ab')) {
            return false;
        }

        flock($fp, LOCK_EX);

        // Instantiating DateTime with microseconds appended to initial date is needed for proper support of this format
        if (strpos($this->_date_fmt, 'u') !== false) {
            $microtime_full = microtime(true);
            $microtime_short = sprintf("%06d", ($microtime_full - floor($microtime_full)) * 1000000);
            $date = new DateTime(date('Y-m-d H:i:s.'.$microtime_short, $microtime_full));
            $date = $date->format($this->_date_fmt);
        } else {
            $date = date($this->_date_fmt);
        }

        $message .= $this->_format_line($p_level, $date, $p_msg);

        for ($written = 0, $length = flcStrUtils::strlen(self::$func_override, $message); $written < $length; $written += $result) {
            if (($result = fwrite($fp, flcStrUtils::substr(self::$func_override, $message, $written))) === false) {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        if (isset($newfile) && $newfile === true) {
            chmod($filepath, $this->_file_permissions);
        }

        return is_int($result);
    }

    // --------------------------------------------------------------------

    /**
     * Format the log line.
     *
     * This is for extensibility of log formatting
     * If you want to change the log format, extend the CI_Log class and override this method
     *
     * @param string $p_level The error level
     * @param string $p_date Formatted date string
     * @param string $p_message The log message
     *
     * @return    string    Formatted log line with a new line character '\n' at the end
     */
    protected function _format_line(string $p_level, string $p_date, string $p_message): string {
        return $p_level.' - '.$p_date.' --> '.$p_message."\n";
    }

}
