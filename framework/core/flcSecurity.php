<?php

namespace framework\core;

use Exception;
use framework\flcCommon;

require_once dirname(__FILE__).'/../flcCommon.php';

class flcSecurity {

    /**
     * CSRF Hash
     *
     * Random hash for Cross Site Request Forgery protection cookie
     *
     * @var    string
     */
    protected string $_csrf_hash;

    /**
     * CSRF Token name
     *
     * Token name for Cross Site Request Forgery protection cookie.
     *
     * @var    string
     */
    protected string $_csrf_token_name = 'flc_csrf_token';

    /**
     * CSRF Expire time
     *
     * Expiration time for Cross Site Request Forgery protection cookie.
     * Defaults to two hours (in seconds).
     *
     * @var    int
     */
    protected int $_csrf_expire = 7200;

    /**
     * CSRF Cookie name
     *
     * Cookie name for Cross Site Request Forgery protection cookie.
     *
     * @var    string
     */
    protected string $_csrf_cookie_name = 'flc_csrf_token';

    // --------------------------------------------------------------------

    /**
     * Class constructor
     *
     * @return    void
     * @throws Exception config problems
     */
    public function __construct() {
        $config = FLC::get_instance()->get_config();

        // Is CSRF protection enabled?
        if ($config->item('csrf_protection')) {
            // CSRF config
            foreach (['csrf_expire', 'csrf_token_name', 'csrf_cookie_name'] as $key) {
                if (null !== ($val = $config->item($key))) {
                    $this->{'_'.$key} = $val;
                }
            }

            // Append application specific cookie prefix
            if ($cookie_prefix = $config->item('cookie_prefix')) {
                $this->_csrf_cookie_name = $cookie_prefix.$this->_csrf_cookie_name;
            }

            // Set the CSRF hash
            $this->_csrf_set_hash();
        }

        flcCommon::log_message('info', 'flcSecurity->_constructor - Security Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * CSRF Verify
     *
     * @return bool
     * @throws Exception when the operation is not allowed
     */
    public function csrf_verify(): bool {
        $config = FLC::get_instance()->get_config();
        // If it's not a POST request we will set the CSRF cookie
        if (strtoupper($_SERVER['REQUEST_METHOD']) !== 'POST') {
            return $this->csrf_set_cookie();
        }


        // Check CSRF token validity, but don't error on mismatch just yet - we'll want to regenerate
        $valid = isset($_POST[$this->_csrf_token_name], $_COOKIE[$this->_csrf_cookie_name]) && hash_equals($_POST[$this->_csrf_token_name], $_COOKIE[$this->_csrf_cookie_name]);

        // We kill this since we're done and we don't want to pollute the _POST array
        unset($_POST[$this->_csrf_token_name]);

        // Regenerate on every submission?
        if ($config->item('csrf_regenerate')) {
            // Nothing should last forever
            unset($_COOKIE[$this->_csrf_cookie_name]);
            $this->_csrf_hash = null;
        }

        $this->_csrf_set_hash();
        $this->csrf_set_cookie();

        if ($valid !== true) {
            throw new Exception('The action you have requested is not allowed.');
        }

        log_message('info', 'CSRF token verified');

        return true;
    }


    // --------------------------------------------------------------------

    /**
     * CSRF Set Cookie
     *
     * @return bool
     * @throws Exception when set cookie fails
     */
    public function csrf_set_cookie(): bool {
        $config = FLC::get_instance()->get_config();

        $expire = time() + $this->_csrf_expire;
        $secure_cookie = (bool)$config->item('cookie_secure');

        if ($secure_cookie && !flcCommon::is_https()) {
            return false;
        }

        setcookie($this->_csrf_cookie_name, $this->_csrf_hash, $expire, $config->item('cookie_path'), $config->item('cookie_domain'), $secure_cookie, $config->item('cookie_httponly'));
        flcCommon::log_message('info', 'flcSecurity->csrf_set_cookie - CSRF cookie sent');

        return true;
    }


    // --------------------------------------------------------------------

    /**
     * Set CSRF Hash and Cookie
     *
     * @return    string
     */
    protected function _csrf_set_hash(): string {
        if ($this->_csrf_hash === null) {
            // If the cookie exists we will use its value.
            // We don't necessarily want to regenerate it with
            // each page load since a page could contain embedded
            // sub-pages causing this feature to fail
            if (isset($_COOKIE[$this->_csrf_cookie_name]) && is_string($_COOKIE[$this->_csrf_cookie_name]) && preg_match('#^[0-9a-f]{32}$#iS', $_COOKIE[$this->_csrf_cookie_name]) === 1) {
                return $this->_csrf_hash = $_COOKIE[$this->_csrf_cookie_name];
            }

            $rand = $this->get_random_bytes(16);
            $this->_csrf_hash = ($rand === null) ? md5(uniqid(mt_rand(), true)) : bin2hex($rand);
        }

        return $this->_csrf_hash;
    }


    // --------------------------------------------------------------------

    /**
     * Get random bytes
     *
     * @param int $p_length Output length
     *
     * @return    string|null
     */
    public function get_random_bytes(int $p_length): ?string {
        if (empty($p_length) or !ctype_digit((string)$p_length)) {
            return null;
        }

        // random bytes is used , this library is for php 7 or greater
        try {
            // The cast is required to avoid TypeError
            return random_bytes($p_length);
        } catch (Exception $e) {
            // If random_bytes() can't do the job, we can't either ...
            // There's no point in using fallbacks.
            flcCommon::log_message('error', 'flcSecurity->get_random_bytes : '.$e->getMessage());

            return null;
        }
    }


}