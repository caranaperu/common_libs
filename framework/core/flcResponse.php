<?php

/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Based in codeigniter , all kudos for his authors
 *
 * @author Codeigniter Team, modified by Carlos Arana Reategui.
 *
 */

namespace framework\core;

use DateTime;
use DateTimeZone;
use Exception;
use framework\flcCommon;


/**
 * Output Class
 * Only can exist one instance per hit , because of that is a singleton
 *
 * Responsible for sending final output to the browser.
 *
 */
class flcResponse {

    use flcMessageTrait;

    /**
     * Reference to the FLC singleton
     *
     * @var    flcResponse
     */
    private static flcResponse $_instance;


    /**
     * Final output string
     *
     * @var    string
     */
    protected string $final_output = '';


    /**
     * List of mime types
     *
     * @var    array
     */
    protected array $mimes = [];


    /**
     * php.ini zlib.output_compression flag
     *
     * @var    bool
     */
    protected bool $_zlib_oc = false;

    /**
     * CI output compression flag
     *
     * @var    bool
     */
    protected bool $_compress_output = false;


    /**
     * mbstring.func_override flag
     *
     * @var    bool
     */
    protected static bool $func_override;


    /**
     * Class constructor
     *
     * Determines whether zLib output compression will be used.
     *
     * @return    void
     * @throws Exception
     */
    private function __construct() {
        self::$_instance =& $this;

        $this->_zlib_oc = (bool)ini_get('zlib.output_compression');
        $this->_compress_output = ($this->_zlib_oc === false && flcCommon::get_config()->item('compress_output') === true && extension_loaded('zlib'));

        isset(self::$func_override) or self::$func_override = (extension_loaded('mbstring') && ini_get('mbstring.func_override'));

        // Get mime types for later
        $this->mimes =& flcCommon::get_mimes();


        flcCommon::log_message('info', 'flcResponse->_construct - Response class initialized');
    }

    // --------------------------------------------------------------------

    /**
     * Return the singletoin instance, previously initialized.
     *
     * @return flcResponse
     */
    public static function &get_instance(): flcResponse {
        static $loaded = false;
        if (!$loaded) {
            new flcResponse();
            $loaded = true;
        }

        return self::$_instance;
    }

    // --------------------------------------------------------------------

    /**
     * Get Output
     *
     * Returns the current output string, this can be called at any time but no guarantee
     * that is in the full final state , for example
     *
     * @return    string
     */
    public function get_output(): string {
        return $this->final_output;
    }

    // --------------------------------------------------------------------

    /**
     * Set Output
     *
     * Sets the output string.
     *
     * @param string $p_output Output data
     *
     * @return    void
     */
    public function set_output(string $p_output) {
        $this->final_output = $p_output;
    }

    // --------------------------------------------------------------------

    /**
     * Append Output
     *
     * Appends data onto the output string.
     *
     * @param string $p_output Data to append
     *
     * @return    void
     */
    public function append_output(string $p_output) {
        $this->final_output .= $p_output;
    }


    // --------------------------------------------------------------------

    /**
     * Set cookie
     *
     * Accepts an arbitrary number of parameters (up to 7) or an associative
     * array in the first parameter containing all the values.
     *
     * @param string|array[] $p_name Cookie name or an array containing parameters
     * @param string         $p_value Cookie value
     * @param int            $p_expire Cookie expiration time in seconds
     * @param string         $p_domain Cookie domain (e.g.: '.yourdomain.com')
     * @param string         $p_path Cookie path (default: '/')
     * @param string         $p_prefix Cookie name prefix
     * @param bool           $p_secure Whether to only transfer cookies via SSL
     * @param bool           $p_httponly Whether to only makes the cookie accessible via HTTP (no javascript)
     *
     * @return    void
     */
    public function set_cookie($p_name, string $p_value = '', int $p_expire = 0, string $p_domain = '', string $p_path = '/', string $p_prefix = '', bool $p_secure = false, bool $p_httponly = false) {
        if (is_array($p_name)) {
            // always leave 'name' in last place, as the loop will break otherwise, due to $$item
            foreach (['value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name'] as $item) {
                if (isset($p_name[$item])) {
                    $$item = $p_name[$item];
                }
            }
        }

        if ($p_prefix === '' && config_item('cookie_prefix') !== '') {
            $p_prefix = config_item('cookie_prefix');
        }

        if ($p_domain == '' && config_item('cookie_domain') != '') {
            $p_domain = config_item('cookie_domain');
        }

        if ($p_path === '/' && config_item('cookie_path') !== '/') {
            $p_path = config_item('cookie_path');
        }

        if ($p_secure === false && config_item('cookie_secure') === true) {
            $p_secure = config_item('cookie_secure');
        }

        if ($p_httponly === false && config_item('cookie_httponly') !== false) {
            $p_httponly = config_item('cookie_httponly');
        }

        if (!is_numeric($p_expire)) {
            $p_expire = time() - 86500;
        } else {
            $p_expire = ($p_expire > 0) ? time() + $p_expire : 0;
        }

        setcookie($p_prefix.$p_name, $p_value, $p_expire, $p_path, $p_domain, $p_secure, $p_httponly);
    }

    // --------------------------------------------------------------------

    /**
     * Return output buffer
     *
     * Processes and return finalized output data along
     * with any server headers.
     *
     * @param string $p_output Output data override
     *
     * @return    string with the buffered final output.
     */
    public function get_final_output(string $p_output = ''): string {

        // Set the output data
        if ($p_output === '') {
            $p_output =& $this->final_output;
        }

        // Is compression requested?
        if ($this->_compress_output === true && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            ob_start('ob_gzhandler');
        }

        $this->send_headers();

        return $p_output;
    }

    // --------------------------------------------------------------------

    /**
     * Sends the headers of this HTTP response to the browser.
     *
     */
    public function send_headers() {
        // Have the headers already been sent?
        if (headers_sent()) {
            return;
        }

        // Per spec, MUST be sent with each request, if possible.
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec13.html
        if (!isset($this->headers['Date']) && PHP_SAPI !== 'cli-server') {
            $date = DateTime::createFromFormat('U', (string)time());
            $date->setTimezone(new DateTimeZone('UTC'));

            $this->set_header('Date', $date->format('D, d M Y H:i:s') . ' GMT');
        }

        // HTTP Status
        header(sprintf('HTTP/%s %s %s', $this->get_protocol_version(), $this->get_status_code(), $this->get_reason_phrase()), true, $this->get_status_code());

        // Send all of our headers
        foreach (array_keys($this->get_headers()) as $name) {
            header($name.': '.$this->get_header_line($name), false, $this->get_status_code());
        }
    }
}
