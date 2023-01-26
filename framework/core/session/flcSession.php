<?php

/**
 * This file is part of CodeIgniter 4 framework.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Modified by Carlos Arana for lib compatability
 *
 */

namespace framework\core\session;


use Exception;
use framework\core\flcConfig;
use framework\core\flcResponse;
use framework\flcCommon;
use \SessionHandlerInterface;




/**
 * Implementation based on CodeIgniter session container.
 *
 * Session configuration is done through session variables and cookie related
 * variables in app/config/config.php
 */
class flcSession implements flcSessionInterface {

    /**
     * Instance of the driver to use.
     *
     * @var SessionHandlerInterface
     */
    protected \SessionHandlerInterface $driver;

    /**
     * The storage driver to use: files, database, redis, memcached
     *
     * @var string
     */
    protected string $session_driver_name;

    /**
     * The session cookie name, must contain only [0-9a-z_-] characters.
     *
     * @var string
     */
    protected string $session_cookie_name = 'flc_session';

    /**
     * The number of SECONDS you want the session to last.
     * Setting it to 0 (zero) means expire when the browser is closed.
     *
     * @var int
     */
    protected int $session_expiration = 7200;

    /**
     * The location to save sessions to, driver dependent..
     *
     * For the 'files' driver, it's a path to a writable directory.
     * WARNING: Only absolute paths are supported!
     *
     * For the 'database' driver, it's a table name.
     **
     * IMPORTANT: You are REQUIRED to set a valid save path!
     *
     * @var string
     */
    protected string $session_save_path;

    /**
     * Whether to match the user's IP address when reading the session data.
     *
     * WARNING: If you're using the database driver, don't forget to update
     * your session table's PRIMARY KEY when changing this setting.
     *
     * @var bool
     */
    protected bool $session_match_ip = false;

    /**
     * How many seconds between CI regenerating the session ID.
     *
     * @var int
     */
    protected int $session_time_to_update = 300;

    /**
     * Whether to destroy session data associated with the old session ID
     * when auto-regenerating the session ID. When set to FALSE, the data
     * will be later deleted by the garbage collector.
     *
     * @var bool
     */
    protected bool $session_regenerate_destroy = false;

    /**
     * The session cookie instance.
     *
     * @var array
     */
    protected array $cookie;


    /**
     * sid regex expression
     *
     * @var string
     */
    protected string $sid_regexp;


    /**
     * Constructor.
     *
     * Extract configuration settings and save them here.
     *
     * @param SessionHandlerInterface $driver the session handler interface instance.
     * @param flcConfig               $p_config the configuration with the cookie and session configuration.
     */
    public function __construct(SessionHandlerInterface $driver, flcConfig $p_config) {

        $this->driver = $driver;

        $this->session_driver_name = $p_config->item('sess_driver');
        $this->session_cookie_name = $p_config->item('sess_cookie_name') ?? $this->session_cookie_name;
        $this->session_expiration = $p_config->item('sess_expiration') ?? $this->session_expiration;
        $this->session_save_path = $p_config->item('sess_save_path');
        $this->session_match_ip = $p_config->item('sess_match_ip') ?? $this->session_match_ip;
        $this->session_time_to_update = $p_config->item('sess_time_to_update') ?? $this->session_time_to_update;
        $this->session_regenerate_destroy = $p_config->item('sess_regenerate_destroy') ?? $this->session_regenerate_destroy;


        $this->cookie = [
            'name' => $this->session_cookie_name,
            'value' => '',
            'expires' => $this->session_expiration === 0 ? 0 : time() + $this->session_expiration,
            'path' => $p_config->item('cookie_path'),
            'domain' => $p_config->item('cookie_domain'),
            'secure' => $p_config->item('cookie_secure'),
            'httponly' => true, // for security
            'samesite' => $p_config->item('cookie_same_site') ?? 'lax',
            'raw' => $cookie->raw ?? false,
        ];

    }

    /**
     * Initialize the session container and starts up the session.
     *
     * @return flcSession|null
     * @throws Exception
     */
    public function start(): ?flcSession {
        if (flcCommon::is_cli() && ENVIRONMENT !== 'testing') {
            flcCommon::log_message('debug', 'Session: Initialization under CLI aborted.');

            return null;
        }

        if ((bool)ini_get('session.auto_start')) {
            flcCommon::log_message('error', 'Session: session.auto_start is enabled in php.ini. Aborting.');

            return null;
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            flcCommon::log_message('info', 'session.auto_start is enabled in php.ini. Aborting.');

            return null;
        }

        $this->configure();
        $this->set_save_handler();

        // Sanitize the cookie, because apparently PHP doesn't do that for userspace handlers
        if (isset($_COOKIE[$this->session_cookie_name]) && (!is_string($_COOKIE[$this->session_cookie_name]) || !preg_match('#\A'.$this->sid_regexp.'\z#', $_COOKIE[$this->session_cookie_name]))) {
            unset($_COOKIE[$this->session_cookie_name]);
        }

        $this->start_session();

        // Is session ID auto-regeneration configured? (ignoring ajax requests)
        if ((empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') && ($regenerateTime = $this->session_time_to_update) > 0) {
            if (!isset($_SESSION['__flc_last_regenerate'])) {
                $_SESSION['__flc_last_regenerate'] = time();
            } elseif ($_SESSION['__flc_last_regenerate'] < (time() - $regenerateTime)) {
                $this->regenerate((bool)$this->session_regenerate_destroy);
            }
        }
        // Another work-around ... PHP doesn't seem to send the session cookie
        // unless it is being currently created or regenerated
        elseif (isset($_COOKIE[$this->session_cookie_name]) && $_COOKIE[$this->session_cookie_name] === session_id()) {
            $this->set_cookie();
        }

        $this->init_vars();
        flcCommon::log_message('info', "Session: Class initialized using '".$this->session_driver_name."' driver.");

        return $this;
    }

    /**
     * Does a full stop of the session:
     *
     * - destroys the session
     * - unsets the session id
     * - destroys the session cookie
     */
    public function stop() {
        setcookie($this->session_cookie_name, session_id(), [
            'expires' => 1,
            'path' => $this->cookie['path'],
            'domain' => $this->cookie['domain'],
            'secure' => $this->cookie['secure'],
            'httponly' => true
        ]);

        session_regenerate_id(true);
    }

    /**
     * Configuration.
     *
     * Handle input binds and configuration defaults.
     */
    protected function configure() {
        if (empty($this->session_cookie_name)) {
            $this->session_cookie_name = ini_get('session.name');
        } else {
            ini_set('session.name', $this->session_cookie_name);
        }

        $same_site = $this->cookie['samesite'] ?: 'Lax';

        $params = [
            'lifetime' => $this->session_expiration,
            'path' => $this->cookie['path'],
            'domain' => $this->cookie['domain'],
            'secure' => $this->cookie['secure'],
            'httponly' => true, // HTTP only; Yes, this is intentional and not configurable for security reasons.
            'samesite' => $same_site,
        ];

        ini_set('session.cookie_samesite', $same_site);
        session_set_cookie_params($params);

        if (!isset($this->session_expiration)) {
            $this->session_expiration = (int)ini_get('session.gc_maxlifetime');
        } elseif ($this->session_expiration > 0) {
            ini_set('session.gc_maxlifetime', (string)$this->session_expiration);
        }

        if (!empty($this->session_save_path)) {
            ini_set('session.save_path', $this->session_save_path);
        }

        // Security is king
        ini_set('session.use_trans_sid', '0');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_cookies', '1');
        ini_set('session.use_only_cookies', '1');

        $this->configure_sid_length();
    }

    /**
     * Configure session ID length
     *
     * To make life easier, we used to force SHA-1 and 4 bits per
     * character on everyone. And of course, someone was unhappy.
     *
     * Then PHP 7.1 broke backwards-compatibility because ext/session
     * is such a mess that nobody wants to touch it with a pole stick,
     * and the one guy who does, nobody has the energy to argue with.
     *
     * So we were forced to make changes, and OF COURSE something was
     * going to break and now we have this pile of shit. -- Narf
     */
    protected function configure_sid_length() {
        $bitsPerCharacter = (int)(ini_get('session.sid_bits_per_character') !== false ? ini_get('session.sid_bits_per_character') : 4);

        $sidLength = (int)(ini_get('session.sid_length') !== false ? ini_get('session.sid_length') : 40);

        if (($sidLength * $bitsPerCharacter) < 160) {
            $bits = ($sidLength * $bitsPerCharacter);
            // Add as many more characters as necessary to reach at least 160 bits
            $sidLength += (int)ceil((160 % $bits) / $bitsPerCharacter);
            ini_set('session.sid_length', (string)$sidLength);
        }

        // Yes, 4,5,6 are the only known possible values as of 2016-10-27
        switch ($bitsPerCharacter) {
            case 4:
                $this->sid_regexp = '[0-9a-f]';
                break;

            case 5:
                $this->sid_regexp = '[0-9a-v]';
                break;

            case 6:
                $this->sid_regexp = '[0-9a-zA-Z,-]';
                break;
        }

        $this->sid_regexp .= '{'.$sidLength.'}';
    }

    /**
     * Handle temporary variables
     *
     * Clears old "flash" data, marks the new one for deletion and handles
     * "temp" data deletion.
     * TODO : no flashdata in this implementation, need remove?
     */
    protected function init_vars() {
        if (empty($_SESSION['__flc_vars'])) {
            return;
        }

        $currentTime = time();

        foreach ($_SESSION['__flc_vars'] as $key => $value) {
            if ($value === 'new') {
                $_SESSION['__flc_vars'][$key] = 'old';
            } // DO NOT move this above the 'new' check!
            elseif ($value === 'old' || $value < $currentTime) {
                unset($_SESSION[$key], $_SESSION['__flc_vars'][$key]);
            }
        }

        if (empty($_SESSION['__flc_vars'])) {
            unset($_SESSION['__flc_vars']);
        }
    }

    /**
     * @inheritdoc
     */
    public function regenerate(bool $p_destroy = false) {
        $_SESSION['__flc_last_regenerate'] = time();
        session_regenerate_id($p_destroy);
    }

    /**
     * Destroys the current session.
     */
    public function destroy() {
        if (ENVIRONMENT === 'testing') {
            return;
        }

        session_destroy();
    }

    /**
     * @inheritdoc
     */
    public function set($p_data, $p_value = null) {
        if (is_array($p_data)) {
            foreach ($p_data as $key => $p_value) {
                if (is_int($key)) {
                    $_SESSION[$p_value] = null;
                } else {
                    $_SESSION[$key] = $p_value;
                }
            }

            return;
        }

        $_SESSION[$p_data] = $p_value;
    }

    /**
     * @inheritdoc
     */
    public function get(?string $p_key = null) {
        if (!empty($p_key) && (null !== ($value = $_SESSION[$p_key] ?? null) || null !== ($value = $this->_array_dot_search($p_key, $_SESSION ?? [])))) {
            return $value;
        }

        if (empty($_SESSION)) {
            return $p_key === null ? [] : null;
        }

        if (!empty($p_key)) {
            return null;
        }

        $userdata = [];

        $keys = array_keys($_SESSION);

        foreach ($keys as $p_key) {
            $userdata[$p_key] = $_SESSION[$p_key];
        }

        return $userdata;
    }

    /**
     * @inheritdoc
     */
    public function has(string $p_key): bool {
        return isset($_SESSION[$p_key]);
    }

    /**
     * Push new value onto session value that is array.
     *
     * @param string $key Identifier of the session property we are interested in.
     * @param array  $data value to be pushed to existing session key.
     */
    public function push(string $key, array $data) {
        if ($this->has($key) && is_array($value = $this->get($key))) {
            $this->set($key, array_merge($value, $data));
        }
    }

    /**
     * @inheritdoc
     */
    public function remove($p_key) {
        if (is_array($p_key)) {
            foreach ($p_key as $k) {
                unset($_SESSION[$k]);
            }

            return;
        }

        unset($_SESSION[$p_key]);
    }

    /**
     * Magic method to set variables in the session by simply calling
     *  $session->foo = bar;
     *
     * @param string       $key Identifier of the session property to set.
     * @param array|string $value
     */
    public function __set(string $key, $value) {
        $_SESSION[$key] = $value;
    }

    /**
     * Magic method to get session variables by simply calling
     *  $foo = $session->foo;
     *
     * @param string $key Identifier of the session property to remove.
     *
     * @return string|null
     */
    public function __get(string $key) {
        // Note: Keep this order the same, just in case somebody wants to
        // use 'session_id' as a session data key, for whatever reason
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }

        if ($key === 'session_id') {
            return session_id();
        }

        return null;
    }

    /**
     * Magic method to check for session variables.
     * Different from has() in that it will validate 'session_id' as well.
     * Mostly used by internal PHP functions, users should stick to has()
     *
     * @param string $key Identifier of the session property to remove.
     */
    public function __isset(string $key): bool {
        return isset($_SESSION[$key]) || ($key === 'session_id');
    }


    /**
     * Sets the driver as the session handler in PHP.
     * Extracted for easier testing.
     */
    protected function set_save_handler() {
        session_set_save_handler($this->driver, true);
    }

    /**
     * Starts the session.
     * Extracted for testing reasons.
     */
    protected function start_session() {
        if (ENVIRONMENT === 'testing') {
            $_SESSION = [];

            return;
        }

        session_start(); // @codeCoverageIgnore
    }

    /**
     * Takes care of setting the cookie on the client side.
     *
     * @codeCoverageIgnore
     */
    protected function set_cookie() {
        // Add specific session stuff to cookie
        $this->cookie['value'] = session_id();
        $this->cookie['expire'] = $this->session_expiration === 0 ? 0 : time() + $this->session_expiration;

        flcResponse::get_instance()->set_cookie($this->cookie);

    }


    /********************************************************
     * Helper
     */

    /**
     * @param string $index
     * @param array  $array
     *
     * @return array|null
     */
    private function _array_dot_search(string $index, array $array): ?array {

        if (empty($index) || count($array) == 0) {
            return null;
        }

        $akeys = array_keys($array);
        // get the keys that fall in the search.
        $akeys = preg_grep("/$index/i", $akeys);

        $data = [];
        if ($akeys) {
            foreach ($akeys as $key) {
                $data[] = $array[$key];
            }

            // return the values.
            return $data;
        }

        return null;
    }
}
