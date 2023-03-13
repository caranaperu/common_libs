<?php

/**
 *
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation < admin@codeigniter.com >
 *
 * for the full copyright and license information, please view * the LICENSE file that was distributed with this source
 * code.
 *
 * Modified by Carlos Arana for lib compatability
 *
 */

namespace flc\core\session\handler;


use Exception;
use flc\flcCommon;
use RuntimeException;



/**
 * Session handler using file system for storage
 */
class flcFileHandler extends flcBaseHandler {
    /**
     * Where to save the session files to.
     *
     * @var string
     */
    protected $save_path;

    /**
     * The file handle
     *
     * @var resource|null
     */
    protected $file_handle;

    /**
     * File Name
     *
     * @var string
     */
    protected string $file_path;

    /**
     * Whether this is a new file.
     *
     * @var bool
     */
    protected bool $file_new;

    /**
     * Whether IP addresses should be matched.
     *
     * @var bool
     */
    protected bool $match_ip = false;

    /**
     * Regex of session ID
     *
     * @var string
     */
    protected string $session_id_regex = '';

    public function __construct(string $p_ip_ddress) {
        parent::__construct($p_ip_ddress);

        // save_path initialized in parent.
        $session_path = $this->save_path;

        if (!empty($session_path)) {
            $this->save_path = rtrim($session_path, '/\\');
            ini_set('session.save_path', $session_path);
        } else {
            $session_path = rtrim(ini_get('session.save_path'), '/\\');

            if (!$session_path) {
                $session_path = WRITEPATH.'session';
            }

            $this->save_path = $session_path;
        }

        $this->configure_session_id_Regex();
    }

    // --------------------------------------------------------------------

    /**
     * Re-initialize existing session, or creates a new one.
     *
     * @param string $p_save_path The path where to store/retrieve the session
     * @param string $p_session_name The session name
     *
     */
    public function open($p_save_path, $p_session_name): bool {
        if (!is_dir($p_save_path) && !mkdir($p_save_path, 0700, true)) {
            throw new RuntimeException('Invalid save path for file session - SES0001');
        }

        if (!is_writable($p_save_path)) {
            throw new RuntimeException('save path for file session is write protected - SES0002');
        }

        $this->save_path = $p_save_path;

        // we'll use the session name as prefix to avoid collisions
        $this->file_path = $this->save_path.'/'.$p_session_name.($this->match_ip ? md5($this->ip_address) : '');

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Reads the session data from the session storage, and returns the results.
     *
     * @param string $p_session_id The session ID
     *
     * @return false|string Returns an encoded string of the read data.
     *                      If nothing was read, it must return false.
     * @throws Exception
     */
    public function read($p_session_id) {
        // This might seem weird, but PHP 5.6 introduced session_reset(),
        // which re-reads session data
        if ($this->file_handle === null) {
            $this->file_new = !is_file($this->file_path.$p_session_id);

            if (($this->file_handle = fopen($this->file_path.$p_session_id, 'c+b')) === false) {
                flcCommon::log_message('error',"Session: Unable to open file '".$this->file_path.$p_session_id."'.");

                return false;
            }

            if (flock($this->file_handle, LOCK_EX) === false) {
                flcCommon::log_message('error',"Session: Unable to obtain lock for file '".$this->file_path.$p_session_id."'.");
                fclose($this->file_handle);
                $this->file_handle = null;

                return false;
            }

            if (!isset($this->session_id)) {
                $this->session_id = $p_session_id;
            }

            if ($this->file_new) {
                chmod($this->file_path.$p_session_id, 0600);
                $this->finger_print = md5('');

                return '';
            }
        } else {
            rewind($this->file_handle);
        }

        $data = '';
        clearstatcache(); // Address https://github.com/codeigniter4/CodeIgniter4/issues/2056

        for ($read = 0, $length = filesize($this->file_path.$p_session_id); $read < $length; $read += strlen($buffer)) {
            if (($buffer = fread($this->file_handle, $length - $read)) === false) {
                break;
            }

            $data .= $buffer;
        }

        $this->finger_print = md5($data);

        return $data;
    }

    // --------------------------------------------------------------------

    /**
     * Writes the session data to the session storage.
     *
     * @param string $p_session_id The session ID
     * @param string $p_data The encoded session data
     *
     * @return bool if no problems
     * @throws Exception
     */
    public function write($p_session_id, $p_data): bool {
        // If the two IDs don't match, we have a session_regenerate_id() call
        if ($p_session_id !== $this->session_id) {
            $this->session_id = $p_session_id;
        }

        if (!is_resource($this->file_handle)) {
            return false;
        }

        if ($this->finger_print === md5($p_data)) {
            return ($this->file_new) ? true : touch($this->file_path.$p_session_id);
        }

        if (!$this->file_new) {
            ftruncate($this->file_handle, 0);
            rewind($this->file_handle);
        }

        if (($length = strlen($p_data)) > 0) {
            $result = null;

            for ($written = 0; $written < $length; $written += $result) {
                if (($result = fwrite($this->file_handle, substr($p_data, $written))) === false) {
                    break;
                }
            }

            if (!is_int($result)) {
                $this->finger_print = md5(substr($p_data, 0, $written));
                flcCommon::log_message('error','Session: Unable to write data.');
                return false;
            }
        }

        $this->finger_print = md5($p_data);

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Closes the current session.
     */
    public function close(): bool {
        if (is_resource($this->file_handle)) {
            flock($this->file_handle, LOCK_UN);
            fclose($this->file_handle);

            $this->file_handle = null;
            $this->file_new = false;
        }

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Destroys a session
     *
     * @param string $p_session_id The session ID being destroyed
     */
    public function destroy( $p_session_id): bool {
        if ($this->close()) {
            return is_file($this->file_path.$p_session_id) ? (unlink($this->file_path.$p_session_id) && $this->destroy_cookie()) : true;
        }

        if ($this->file_path !== null) {
            clearstatcache();

            return is_file($this->file_path.$p_session_id) ? (unlink($this->file_path.$p_session_id) && $this->destroy_cookie()) : true;
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Cleans up expired sessions.
     * Call by php itself, is important to set permissions to permit delete files.
     *
     * @param int $p_max_lifetime Sessions that have not updated
     *                          for the last max_lifetime seconds will be removed.
     *
     * @return false|int Returns the number of deleted sessions on success, or false on failure.
     * @throws Exception
     */
    public function gc( $p_max_lifetime) {
        if (!is_dir($this->save_path) || ($directory = opendir($this->save_path)) === false) {
            flcCommon::log_message('debug',"Session: Garbage collector couldn't list files under directory '".$this->save_path."'.");

            return false;
        }

        $ts = time() - $p_max_lifetime;

        $pattern = $this->match_ip === true ? '[0-9a-f]{32}' : '';

        $pattern = sprintf('#\A%s'.$pattern.$this->session_id_regex.'\z#', preg_quote($this->cookie_name, '#'));

        $collected = 0;

        while (($file = readdir($directory)) !== false) {
            // If the filename doesn't match this pattern, it's either not a session file or is not ours
            if (!preg_match($pattern, $file) || !is_file($this->save_path.DIRECTORY_SEPARATOR.$file) || ($mtime = filemtime($this->save_path.DIRECTORY_SEPARATOR.$file)) === false || $mtime > $ts) {
                continue;
            }

            unlink($this->save_path.DIRECTORY_SEPARATOR.$file);
            $collected++;
        }

        closedir($directory);

        return $collected;
    }

    // --------------------------------------------------------------------

    /**
     * Configure Session ID regular expression
     */
    protected function configure_session_id_Regex() {
        $bitsPerCharacter = (int)ini_get('session.sid_bits_per_character');
        $SIDLength = (int)ini_get('session.sid_length');

        if (($bits = $SIDLength * $bitsPerCharacter) < 160) {
            // Add as many more characters as necessary to reach at least 160 bits
            $SIDLength += (int)ceil((160 % $bits) / $bitsPerCharacter);
            ini_set('session.sid_length', (string)$SIDLength);
        }

        switch ($bitsPerCharacter) {
            case 4:
                $this->session_id_regex = '[0-9a-f]';
                break;

            case 5:
                $this->session_id_regex = '[0-9a-v]';
                break;

            case 6:
                $this->session_id_regex = '[0-9a-zA-Z,-]';
                break;
        }

        $this->session_id_regex .= '{'.$SIDLength.'}';
    }
}
