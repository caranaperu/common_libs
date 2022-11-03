<?php

namespace framework\core;

use framework\flcCommon;
use RuntimeException;

/**
 * Common parts for request and response classes
 */
trait flcMessageTrait {
    /**
     * HTTP status codes
     *
     * @var array
     */
    protected static array $status_codes = [
        // 1xx: Informational
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing', // http://www.iana.org/go/rfc2518
        103 => 'Early Hints', // http://www.ietf.org/rfc/rfc8297.txt
        // 2xx: Success
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information', // 1.1
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status', // http://www.iana.org/go/rfc4918
        208 => 'Already Reported', // http://www.iana.org/go/rfc5842
        226 => 'IM Used', // 1.1; http://www.ietf.org/rfc/rfc3229.txt
        // 3xx: Redirection
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found', // Formerly 'Moved Temporarily'
        303 => 'See Other', // 1.1
        304 => 'Not Modified',
        305 => 'Use Proxy', // 1.1
        306 => 'Switch Proxy', // No longer used
        307 => 'Temporary Redirect', // 1.1
        308 => 'Permanent Redirect', // 1.1; Experimental; http://www.ietf.org/rfc/rfc7238.txt
        // 4xx: Client error
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Content Too Large', // https://www.iana.org/assignments/http-status-codes/http-status-codes.xml
        414 => 'URI Too Long', // https://www.iana.org/assignments/http-status-codes/http-status-codes.xml
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => "I'm a teapot", // April's Fools joke; http://www.ietf.org/rfc/rfc2324.txt
        // 419 (Authentication Timeout) is a non-standard status code with unknown origin
        421 => 'Misdirected Request', // http://www.iana.org/go/rfc7540 Section 9.1.2
        422 => 'Unprocessable Content', // https://www.iana.org/assignments/http-status-codes/http-status-codes.xml
        423 => 'Locked', // http://www.iana.org/go/rfc4918
        424 => 'Failed Dependency', // http://www.iana.org/go/rfc4918
        425 => 'Too Early', // https://datatracker.ietf.org/doc/draft-ietf-httpbis-replay/
        426 => 'Upgrade Required',
        428 => 'Precondition Required', // 1.1; http://www.ietf.org/rfc/rfc6585.txt
        429 => 'Too Many Requests', // 1.1; http://www.ietf.org/rfc/rfc6585.txt
        431 => 'Request Header Fields Too Large', // 1.1; http://www.ietf.org/rfc/rfc6585.txt
        451 => 'Unavailable For Legal Reasons', // http://tools.ietf.org/html/rfc7725
        499 => 'Client Closed Request', // http://lxr.nginx.org/source/src/http/ngx_http_request.h#0133
        // 5xx: Server error
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates', // 1.1; http://www.ietf.org/rfc/rfc2295.txt
        507 => 'Insufficient Storage', // http://www.iana.org/go/rfc4918
        508 => 'Loop Detected', // http://www.iana.org/go/rfc5842
        510 => 'Not Extended', // http://www.ietf.org/rfc/rfc2774.txt
        511 => 'Network Authentication Required', // http://www.ietf.org/rfc/rfc6585.txt
        599 => 'Network Connect Timeout Error', // https://httpstatuses.com/599
    ];

    /**
     * List of valid protocol versions
     *
     * @var array
     */
    protected array $valid_protocol_versions = [
        '1.0',
        '1.1',
        '2.0',
    ];

    /**
     * to save the protocol version
     *
     * @var string
     */
    protected string $protocol_version;

    /**
     * The status code , need to be one of the defined in
     * $status_codes
     *
     * @var int
     */
    protected int $status_code;

    /**
     * @var string
     */
    protected string $reason;

    /**
     * List of all HTTP request headers
     *
     * @var array
     */
    protected array $headers = [];

    /**
     * List of all headers names normalized to lowercase key.
     *
     * @var array
     */
    protected array $header_map = [];


    /**
     * Gets the response response phrase associated with the status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @return string response phrase+status code
     */
    public function get_reason_phrase(): string {
        if ($this->reason === '') {
            return !empty($this->statusCode) ? static::$status_codes[$this->statusCode] : '';
        }

        return $this->reason;
    }

    // ------------------------------------------------------------------------

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the getServer's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function get_status_code(): int {
        if (empty($this->status_code)) {
            throw new RuntimeException("HTTP Response is missing a status code");
        }

        return $this->status_code;
    }

    // ------------------------------------------------------------------------

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, will default recommended reason phrase for
     * the response's status code.
     *
     * @see http://tools.ietf.org/html/rfc7231#section-6
     * @see http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     *
     * @param int    $p_code The 3-digit integer result code to set.
     * @param string $p_reason The reason phrase to use with the
     *                       provided status code; if none is provided, will
     *                       default to the IANA name.
     *
     * @throws RuntimeException For invalid status code arguments.
     *
     */
    public function set_status_code(int $p_code, string $p_reason = '') {
        // Valid range?
        if ($p_code < 100 || $p_code > 599) {
            throw new RuntimeException("$p_code is not a valid HTTP return status code");
        }

        // Unknown and no message?
        if (!array_key_exists($p_code, static::$status_codes) && empty($p_reason)) {
            throw new RuntimeException("Unknown HTTP status code provided with no message: $p_code");
        }

        $this->status_code = $p_code;

        $this->reason = !empty($p_reason) ? $p_reason : static::$status_codes[$p_code];

    }

    // --------------------------------------------------------------------

    /**
     * Sets the HTTP protocol version.
     *
     * @param string $p_version
     *
     * @throws RuntimeException For invalid protocols
     *
     */
    public function set_protocol_version(string $p_version) {
        if (!is_numeric($p_version)) {
            $p_version = substr($p_version, strpos($p_version, '/') + 1);
        }

        // Make sure that version is in the correct format
        $p_version = number_format((float)$p_version, 1);

        if (!in_array($p_version, $this->valid_protocol_versions, true)) {
            $protocols = implode(', ', $this->valid_protocol_versions);
            throw new RuntimeException("Invalid HTTP Protocol Version. Must be one of: $protocols");
        }

        $this->protocol_version = $p_version;

    }

    // --------------------------------------------------------------------

    /**
     * Returns the HTTP Protocol Version.
     *
     * @return string the protocol version
     */
    public function get_protocol_version(): string {
        return $this->protocol_version ?? '1.1';
    }

    // --------------------------------------------------------------------

    /**
     * Populates the $headers array with any headers the getServer knows about.
     *
     */
    public function populate_headers() {
        // If header is already defined, return it immediately
        if (!empty($this->headers)) {
            return;
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
                    $this->header_map[strtolower($header)] = $header;
                }
            }
        }

    }

    // --------------------------------------------------------------------

    /**
     * Get Header
     *
     * Returns the value of a single member of the headers class member
     *
     * @param string $p_name Header name
     *
     * @return    array|string|null    The requested header on success or NULL on failure
     */
    public function get_header(string $p_name) {
        $orig_name = $this->get_header_name($p_name);

        return $this->headers[$orig_name] ?? null;

    }

    // --------------------------------------------------------------------

    /**
     * Set Header
     *
     * Lets you set a server header which will be sent with the final output.
     *
     * Note: If a file is cached, headers will not be sent.
     *
     * @param string       $p_name Header
     * @param array|string $p_value
     *
     * @return    void
     *
     */
    public function set_header(string $p_name, $p_value) {
        // If zlib.output_compression is enabled it will compress the output,
        // but it will not modify the content-length header to compensate for
        // the reduction, causing the browser to hang waiting for more data.
        // We'll just skip content-length in those cases.
        if ($this->_zlib_oc && strncasecmp($p_name, 'content-length', 14) === 0) {
            return;
        }

        $orig_name = $this->get_header_name($p_name);

        if (isset($this->headers[$orig_name]) && is_array($this->headers[$orig_name])) {
            if (!is_array($p_value)) {
                $p_value = [$p_value];
            }

            foreach ($p_value as $v) {
                $this->append_header($orig_name, $v);
            }
        } else {
            $this->headers[$orig_name] = $p_value;
            $this->header_map[strtolower($orig_name)] = $orig_name;
        }
    }

    // --------------------------------------------------------------------

    /**
     * Takes a header name in any case, and returns the
     * normal-case version of the header.
     */
    protected function get_header_name(string $p_name): string {
        return $this->header_map[strtolower($p_name)] ?? $p_name;
    }

    // --------------------------------------------------------------------


    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * This method returns all of the header values of the given
     * case-insensitive header name as a string concatenated together using
     * a comma.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * @param string $p_name
     *
     * @return string
     */
    public function get_header_line(string $p_name): string {
        $origName = $this->get_header_name($p_name);

        if (!array_key_exists($origName, $this->headers)) {
            return '';
        }

        return $this->_get_value_line($this->headers[$origName]);
    }

    // --------------------------------------------------------------------

    /**
     * Adds an additional header value to any headers that accept
     * multiple values (i.e. are an array or implement ArrayAccess)
     *
     * @param string      $p_name header name
     * @param string|null $p_value header value
     *
     */
    public function append_header(string $p_name, ?string $p_value) {
        $origName = $this->get_header_name($p_name);

        if (array_key_exists($origName, $this->headers)) {

            if (!in_array($p_value, $this->headers[$origName], true)) {
                $this->headers[$origName] = $p_value;
            }
        } else {
            $this->set_header($p_name, $p_value);
        }

    }

    // --------------------------------------------------------------------

    /**
     * Set HTTP Status Header
     *
     * @param int    $p_code the status code
     * @param string $p_text text on the header
     *
     * @return    void
     */
    function set_status_header(int $p_code = 200, string $p_text = '') {
        if (flcCommon::is_cli()) {
            return;
        }

       /* if (empty($p_code) or !is_numeric($p_code)) {
            $p_text = 'Status code must be numeric';
            $p_code = 500;
        }*/

        if (empty($p_text)) {
            is_int($p_code) or $p_code = (int)$p_code;

            if (isset(self::$status_codes[$p_code])) {
                $p_text = self::$status_codes[$p_code];
            } else {
                $p_text = '(null)';
                $p_code = 500;
            }
        }

        if (strpos(PHP_SAPI, 'cgi') === 0) {
            header('Status: '.$p_code.' '.$p_text, true);
        } else {
            header($this->get_protocol_version().' '.$p_code.' '.$p_text, true, $p_code);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Returns an array containing all Headers.
     *
     * @return array<string, mixed>
     */
    public function get_headers(): array {
        if (empty($this->headers)) {
            $this->populate_headers();
        }

        return $this->headers;
    }

    // --------------------------------------------------------------------

    /*********************************************************************
     * Helpers
     */

    /**
     * Retrieves a comma-separated string of the values for a single header.
     *
     * NOTE: Not all header values may be appropriately represented using
     * comma concatenation. For such headers, use getHeader() instead
     * and supply your own delimiter when concatenating.
     *
     * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2
     *
     * @param array|string $p_header_val
     *
     * @return string
     */
    private function _get_value_line($p_header_val): string {
        if (is_string($p_header_val)) {
            return $p_header_val;
        }
        if (!is_array($p_header_val)) {
            return '';
        }

        $options = [];

        foreach ($p_header_val as $key => $value) {
            if (is_string($key) && !is_array($value)) {
                $options[] = $key.'='.$value;
            } elseif (is_array($value)) {
                $key = key($value);
                $options[] = $key.'='.$value[$key];
            } elseif (is_numeric($key)) {
                $options[] = $value;
            }
        }

        return implode(', ', $options);
    }
    // --------------------------------------------------------------------

}