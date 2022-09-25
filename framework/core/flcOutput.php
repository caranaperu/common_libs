<?php

namespace framework\core;

use Exception;
use framework\flcCommon;

include_once dirname(__FILE__).'/FLC.php';
include_once dirname(__FILE__).'/../flcCommon.php';

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2014 - 2017, British Columbia Institute of Technology
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2017, British Columbia Institute of Technology (http://bcit.ca/)
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Output Class
 *
 * Responsible for sending final output to the browser.
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Output
 * @author        EllisLab Dev Team
 * @link        https://codeigniter.com/user_guide/libraries/output.html
 */
class flcOutput {

    /**
     * Final output string
     *
     * @var    string
     */
    protected string $final_output = '';


    /**
     * List of server headers
     *
     * @var    array
     */
    protected array $headers = [];

    /**
     * List of mime types
     *
     * @var    array
     */
    protected array $mimes = [];

    /**
     * Mime-type for the current page
     *
     * @var    string
     */
    protected string $mime_type = 'text/html';


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
    public function __construct() {
        $this->_zlib_oc = (bool)ini_get('zlib.output_compression');
        $this->_compress_output = ($this->_zlib_oc === false && FLC::get_instance()->get_config()->item('compress_output') === true && extension_loaded('zlib'));

        isset(self::$func_override) or self::$func_override = (extension_loaded('mbstring') && ini_get('mbstring.func_override'));

        // Get mime types for later
        $this->mimes =& flcCommon::get_mimes();

        flcCommon::log_message('info', 'flcOutput->_construct - Output class initialized');
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
     * Set Header
     *
     * Lets you set a server header which will be sent with the final output.
     *
     * Note: If a file is cached, headers will not be sent.
     *
     * @param string $p_header Header
     * @param bool   $p_replace Whether to replace the old header value, if already set
     *
     * @return    void
     *
     */
    public function set_header(string $p_header, bool $p_replace = true) {
        // If zlib.output_compression is enabled it will compress the output,
        // but it will not modify the content-length header to compensate for
        // the reduction, causing the browser to hang waiting for more data.
        // We'll just skip content-length in those cases.
        if ($this->_zlib_oc && strncasecmp($p_header, 'content-length', 14) === 0) {
            return;
        }

        $this->headers[] = [$p_header, $p_replace];
    }

    // --------------------------------------------------------------------

    /**
     * Set Content-Type Header
     *
     * @param string      $p_mime_type Extension of the file we're outputting
     * @param string|null $p_charset Character set (default: null)
     *
     * @return    void
     * @throws Exception config problems
     */
    public function set_content_type(string $p_mime_type, ?string $p_charset = null) {
        if (strpos($p_mime_type, '/') === false) {
            $extension = ltrim($p_mime_type, '.');

            // Is this extension supported?
            if (isset($this->mimes[$extension])) {
                $p_mime_type =& $this->mimes[$extension];

                if (is_array($p_mime_type)) {
                    $p_mime_type = current($p_mime_type);
                }
            }
        }

        $this->mime_type = $p_mime_type;

        if (empty($p_charset)) {
            $p_charset = FLC::get_instance()->get_config()->item('charset');
        }

        $header = 'Content-Type: '.$p_mime_type.(empty($p_charset) ? '' : '; charset='.$p_charset);

        $this->headers[] = [$header, true];

    }

    // --------------------------------------------------------------------

    /**
     * Get Current Content-Type Header
     *
     * @return    string    'text/html', if not already set
     */
    public function get_content_type(): string {
        for ($i = 0, $c = count($this->headers); $i < $c; $i++) {
            if (sscanf($this->headers[$i][0], 'Content-Type: %[^;]', $content_type) === 1) {
                return $content_type;
            }
        }

        return 'text/html';
    }

    // --------------------------------------------------------------------

    /**
     * Get Header
     *
     * @param string $p_header
     *
     * @return    string|null
     */
    public function get_header(string $p_header): ?string {
        // Combine headers already sent with our batched headers
        $headers = array_merge(// We only need [x][0] from our multi-dimensional array
            array_map('array_shift', $this->headers), headers_list());

        if (empty($headers) or empty($p_header)) {
            return null;
        }

        // Count backwards, in order to get the last matching header
        for ($c = count($headers) - 1; $c > -1; $c--) {
            if (strncasecmp($p_header, $headers[$c], $l = flcCommon::strlen(self::$func_override, $p_header)) === 0) {
                return trim(flcCommon::substr(self::$func_override, $headers[$c], $l + 1));
            }
        }

        return null;
    }

    // --------------------------------------------------------------------

    /**
     * Set HTTP Status Header
     *
     * As of version 1.7.2, this is an alias for common function
     * set_status_header().
     *
     * @param int    $p_code Status code (default: 200)
     * @param string $p_text Optional message
     *
     * @return    void
     */
    public function set_status_header(int $p_code = 200, string $p_text = '') {
        set_status_header($p_code, $p_text);
    }

    // --------------------------------------------------------------------

    /**
     * Return Display Output
     *
     * Processes and return finalized output data along
     * with any server headers.
     *
     * @param string $p_output Output data override
     *
     * @return    string with the buffered final output.
     * @uses    CI_Output::$final_output
     */
    public function get_final_output(string $p_output = ''): string {

        // Set the output data
        if ($p_output === '') {
            $p_output =& $this->final_output;
        }

        // --------------------------------------------------------------------

        // Is compression requested?
        if ($this->_compress_output === true && isset($_SERVER['HTTP_ACCEPT_ENCODING']) && strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            ob_start('ob_gzhandler');
        }

        // Are there any server headers to send?
        if (count($this->headers) > 0) {
            foreach ($this->headers as $header) {
                @header($header[0], $header[1]);
            }
        }

        return $p_output;
    }


}
