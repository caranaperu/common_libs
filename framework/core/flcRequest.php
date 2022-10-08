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

use Exception;
use framework\flcCommon;

require_once dirname(__FILE__).'/../flcCommon.php';


/**
 * Request Class
 *
 * Pre-processes global input data for security
 *
 */
class flcRequest {

    /**
     * IP address of the current user
     *
     * @var    string
     */
    protected string $ip_address = '';


    /**
     * Enable CSRF flag
     *
     * Enables a CSRF cookie token to be set.
     * Set automatically based on config setting.
     *
     * @var    bool
     */
    protected bool $_enable_csrf = false;

    /**
     * List of all HTTP request headers
     *
     * @var array
     */
    protected array $headers = [];

    /**
     * Raw input stream data
     *
     * Holds a cache of php://input contents
     *
     * @var    string
     */
    protected string $_raw_input_stream;

    /**
     * Parsed input stream data
     *
     * Parsed from php://input at runtime
     *
     * @var    array
     */
    protected array $_input_stream = [];


    // --------------------------------------------------------------------

    /**
     * Class constructor
     *
     * Determines whether to globally enable the XSS processing
     * and whether to allow the $_GET array.
     *
     * @return    void
     * @throws Exception if config cant be loaded
     */
    public function __construct() {

        $this->_enable_csrf = (flcCommon::get_config()->item('csrf_protection') === true);


        // Sanitize global arrays
        $this->_sanitize_globals();

        // CSRF Protection check
        if ($this->_enable_csrf === true && !is_cli()) {
            $security = new flcSecurity();
            $security->csrf_verify();
        }

        flcCommon::log_message('info', 'Input Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * Fetch from array
     *
     * Internal method used to retrieve values from global arrays.
     *
     * @param array    &$p_array $_GET, $_POST, $_COOKIE, $_SERVER, etc.
     * @param mixed     $p_index Index for item to be fetched from $array
     *
     * @return    mixed
     */
    protected function _fetch_from_array(array &$p_array, $p_index = null) {

        // If $index is NULL, it means that the whole $array is requested
        isset($p_index) or $p_index = array_keys($p_array);

        // allow fetching multiple keys at once
        if (is_array($p_index)) {
            $output = [];
            foreach ($p_index as $key) {
                $output[$key] = $this->_fetch_from_array($p_array, $key);
            }

            return $output;
        }

        if (isset($p_array[$p_index])) {
            $value = $p_array[$p_index];
        } elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $p_index, $matches)) > 1) // Does the index contain array notation
        {
            $value = $p_array;
            for ($i = 0; $i < $count; $i++) {
                $key = trim($matches[0][$i], '[]');
                if ($key === '') // Empty notation will return the value as array
                {
                    break;
                }

                if (isset($value[$key])) {
                    $value = $value[$key];
                } else {
                    return null;
                }
            }
        } else {
            return null;
        }

        return $value;
    }

    // --------------------------------------------------------------------

    /**
     * Fetch an item from the GET array
     *
     * @param mixed $p_index Index for item to be fetched from $_GET
     *
     * @return    mixed
     */
    public function get($p_index = null) {
        return $this->_fetch_from_array($_GET, $p_index);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch an item from the POST array
     *
     * @param mixed $p_index Index for item to be fetched from $_POST
     *
     * @return    mixed
     */
    public function post($p_index = null) {
        return $this->_fetch_from_array($_POST, $p_index);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch an item from POST data with fallback to GET
     *
     * @param string $p_index Index for item to be fetched from $_POST or $_GET
     *
     * @return    mixed
     */
    public function post_get(string $p_index) {
        return isset($_POST[$p_index]) ? $this->post($p_index) : $this->get($p_index);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch an item from GET data with fallback to POST
     *
     * @param string $p_index Index for item to be fetched from $_GET or $_POST
     *
     * @return    mixed
     */
    public function get_post(string $p_index) {
        return isset($_GET[$p_index]) ? $this->get($p_index) : $this->post($p_index);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch an item from the COOKIE array
     *
     * @param mixed $p_index Index for item to be fetched from $_COOKIE
     *
     * @return    mixed
     */
    public function cookie($p_index = null) {
        return $this->_fetch_from_array($_COOKIE, $p_index);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch an item from the SERVER array
     *
     * @param mixed $p_index Index for item to be fetched from $_SERVER
     *
     * @return    mixed
     */
    public function server($p_index) {
        return $this->_fetch_from_array($_SERVER, $p_index);
    }

    // ------------------------------------------------------------------------

    /**
     * Fetch an item from the php://input stream
     *
     * Useful when you need to access PUT, DELETE or PATCH request data.
     *
     * @param string|null $p_index Index for item to be fetched
     *
     * @return    mixed
     */
    public function input_stream(?string $p_index = null) {

        return $this->_fetch_from_array($this->_input_stream, $p_index);
    }



    // --------------------------------------------------------------------

    /**
     * Fetch the IP Address
     *
     * Determines and validates the visitor's IP address.
     *
     * @return    string    IP address
     */
    public function ip_address(): string {
        if ($this->ip_address !== '') {
            return $this->ip_address;
        }

        $proxy_ips = config_item('proxy_ips');
        if (!empty($proxy_ips) && !is_array($proxy_ips)) {
            $proxy_ips = explode(',', str_replace(' ', '', $proxy_ips));
        }

        $this->ip_address = $this->server('REMOTE_ADDR');

        if ($proxy_ips) {
            foreach ([
                         'HTTP_X_FORWARDED_FOR',
                         'HTTP_CLIENT_IP',
                         'HTTP_X_CLIENT_IP',
                         'HTTP_X_CLUSTER_CLIENT_IP'
                     ] as $header) {
                if (($spoof = $this->server($header)) !== null) {
                    // Some proxies typically list the whole chain of IP
                    // addresses through which the client has reached us.
                    // e.g. client_ip, proxy_ip1, proxy_ip2, etc.
                    sscanf($spoof, '%[^,]', $spoof);

                    if (!$this->valid_ip($spoof)) {
                        $spoof = null;
                    } else {
                        break;
                    }
                }
            }

            if ($spoof) {
                for ($i = 0, $c = count($proxy_ips); $i < $c; $i++) {
                    // Check if we have an IP address or a subnet
                    if (strpos($proxy_ips[$i], '/') === false) {
                        // An IP address (and not a subnet) is specified.
                        // We can compare right away.
                        if ($proxy_ips[$i] === $this->ip_address) {
                            $this->ip_address = $spoof;
                            break;
                        }

                        continue;
                    }

                    // We have a subnet ... now the heavy lifting begins
                    isset($separator) or $separator = $this->valid_ip($this->ip_address, 'ipv6') ? ':' : '.';

                    // If the proxy entry doesn't match the IP protocol - skip it
                    if (strpos($proxy_ips[$i], $separator) === false) {
                        continue;
                    }

                    // Convert the REMOTE_ADDR IP address to binary, if needed
                    if (!isset($ip, $sprintf)) {
                        if ($separator === ':') {
                            // Make sure we're have the "full" IPv6 format
                            $ip = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($this->ip_address, ':')), $this->ip_address));

                            for ($j = 0; $j < 8; $j++) {
                                $ip[$j] = intval($ip[$j], 16);
                            }

                            $sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
                        } else {
                            $ip = explode('.', $this->ip_address);
                            $sprintf = '%08b%08b%08b%08b';
                        }

                        $ip = vsprintf($sprintf, $ip);
                    }

                    // Split the netmask length off the network address
                    sscanf($proxy_ips[$i], '%[^/]/%d', $netaddr, $masklen);

                    // Again, an IPv6 address is most likely in a compressed form
                    if ($separator === ':') {
                        $netaddr = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($netaddr, ':')), $netaddr));
                        for ($j = 0; $j < 8; $j++) {
                            $netaddr[$j] = intval($netaddr[$j], 16);
                        }
                    } else {
                        $netaddr = explode('.', $netaddr);
                    }

                    // Convert to binary and finally compare
                    if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0) {
                        $this->ip_address = $spoof;
                        break;
                    }
                }
            }
        }

        if (!$this->valid_ip($this->ip_address)) {
            return $this->ip_address = '0.0.0.0';
        }

        return $this->ip_address;
    }

    // --------------------------------------------------------------------

    /**
     * Validate IP Address
     *
     * @param string $p_ip IP address
     * @param string $p_which IP protocol: 'ipv4' or 'ipv6'
     *
     * @return    bool
     */
    public function valid_ip(string $p_ip, string $p_which = ''): bool {
        switch (strtolower($p_which)) {
            case 'ipv4':
                $p_which = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $p_which = FILTER_FLAG_IPV6;
                break;
            default:
                $p_which = null;
                break;
        }

        return (bool)filter_var($p_ip, FILTER_VALIDATE_IP, $p_which);
    }

    // --------------------------------------------------------------------

    /**
     * Fetch User Agent string
     *
     * @return    string|null    User Agent string or NULL if it doesn't exist
     */
    public function user_agent() {
        return $this->_fetch_from_array($_SERVER, 'HTTP_USER_AGENT');
    }

    // --------------------------------------------------------------------

    /**
     * Sanitize Globals
     *
     * Internal method serving for the following purposes:
     *
     *    - Unsets $_GET data, if query strings are not enabled
     *    - Cleans POST, COOKIE and SERVER data
     *    - Standardizes newline characters to PHP_EOL
     *
     * @return    void
     * @throws Exception
     */
    protected function _sanitize_globals() {
        if (is_array($_GET)) {
            foreach ($_GET as $key => $val) {
                $_GET[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
            }
        }

        // Clean $_POST Data
        if (is_array($_POST)) {
            foreach ($_POST as $key => $val) {
                $_POST[$this->_clean_input_keys($key)] = $this->_clean_input_data($val);
            }
        }

        // Clean $_COOKIE Data
        if (is_array($_COOKIE)) {
            // Also get rid of specially treated cookies that might be set by a server
            // or silly application, that are of no use to a CI application anyway
            // but that when present will trip our 'Disallowed Key Characters' alarm
            // http://www.ietf.org/rfc/rfc2109.txt
            // note that the key names below are single quoted strings, and are not PHP variables
            unset($_COOKIE['$Version'], $_COOKIE['$Path'], $_COOKIE['$Domain']);

            foreach ($_COOKIE as $key => $val) {
                if (($cookie_key = $this->_clean_input_keys($key)) !== null) {
                    $_COOKIE[$cookie_key] = $this->_clean_input_data($val);
                } else {
                    unset($_COOKIE[$key]);
                }
            }
        }

        // Sanitize PHP_SELF
        $_SERVER['PHP_SELF'] = strip_tags($_SERVER['PHP_SELF']);

        flcCommon::log_message('debug', 'flcRequest->__constructor - Global POST, GET and COOKIE data sanitized');
    }

    // --------------------------------------------------------------------

    /**
     * Clean Input Data
     *
     * Internal method that aids in escaping data and
     * standardizing newline characters to PHP_EOL.
     *
     * @param string $p_str Input string(s)
     *
     * @return    string
     */
    protected function _clean_input_data(string $p_str): string {

        // Clean UTF-8 if supported
        if (UTF8_ENABLED === true) {
            $p_str = flcUtf8::clean_string($p_str);
        }

        // Remove control characters
        return flcCommon::remove_invisible_characters($p_str, false);
    }

    // --------------------------------------------------------------------

    /**
     * Clean Keys
     *
     * Internal method that helps to prevent malicious users
     * from trying to exploit keys we make sure that keys are
     * only named with alpha-numeric text and a few other items.
     *
     * @param string $p_str Input string
     *
     * @return    string|bool
     */
    protected function _clean_input_keys(string $p_str): ?string {
        if (!preg_match('/^[a-z0-9:_\/|-]+$/i', $p_str)) {
            return null;
        }

        // Clean UTF-8 if supported
        if (UTF8_ENABLED === true) {
            return flcUtf8::clean_string($p_str);
        }

        return $p_str;
    }

    // --------------------------------------------------------------------

    /**
     * Request Headers
     *
     *
     * @return    array
     */
    public function request_headers(): array {
        // If header is already defined, return it immediately
        if (!empty($this->headers)) {
            return $this->_fetch_from_array($this->headers);
        }

        // In Apache, you can simply call apache_request_headers()
        if (function_exists('apache_request_headers')) {
            $this->headers = apache_request_headers();
        } else {
            isset($_SERVER['CONTENT_TYPE']) && $this->headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];

            foreach ($_SERVER as $key => $val) {
                if (sscanf($key, 'HTTP_%s', $header) === 1) {
                    // take SOME_HEADER and turn it into Some-Header
                    $header = str_replace('_', ' ', strtolower($header));
                    $header = str_replace(' ', '-', ucwords($header));

                    $this->headers[$header] = $val;
                }
            }
        }

        return $this->_fetch_from_array($this->headers);
    }

    // --------------------------------------------------------------------

    /**
     * Get Request Header
     *
     * Returns the value of a single member of the headers class member
     *
     * @param string $p_index Header name
     *
     * @return    string|null    The requested header on success or NULL on failure
     */
    public function get_request_header(string $p_index): ?string {
        static $headers;

        if (!isset($headers)) {
            empty($this->headers) && $this->request_headers();
            foreach ($this->headers as $key => $value) {
                $headers[strtolower($key)] = $value;
            }
        }

        $p_index = strtolower($p_index);

        if (!isset($headers[$p_index])) {
            return null;
        }

        return $headers[$p_index];
    }



    // --------------------------------------------------------------------

    /**
     * Is CLI request?
     *
     * Test to see if a request was made from the command line.
     *
     * @return    bool
     */
    public function is_cli_request(): bool {
        return flcCommon::is_cli();
    }

    /**
     * Is Ajax request?
     *
     *
     * @return    bool
     */
    public function is_ajax_request(): bool {
        return flcCommon::is_ajax();
    }


    // --------------------------------------------------------------------

    /**
     * Get Request Method
     *
     * Return the request method
     *
     * @param bool $p_upper Whether to return in upper or lower case
     *                (default: FALSE)
     *
     * @return    string
     */
    public function method(bool $p_upper = false): string {
        return ($p_upper) ? strtoupper($this->server('REQUEST_METHOD')) : strtolower($this->server('REQUEST_METHOD'));
    }

    // ------------------------------------------------------------------------

    /**
     * Magic __get()
     *
     * Allows read access to protected properties
     *
     * @param string $p_name
     *
     * @return    mixed
     */
    public function __get(string $p_name) {
        if ($p_name === 'raw_input_stream') {
            isset($this->_raw_input_stream) or $this->_raw_input_stream = file_get_contents('php://input');

            return $this->_raw_input_stream;
        } elseif ($p_name === 'ip_address') {
            return $this->ip_address;
        }
    }

}
