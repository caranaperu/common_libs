<?php

namespace framework\core;

use framework\flcCommon;

require_once dirname(__FILE__).'/../flcCommon.php';

/**
 * FLabsCode
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2022 - 2022, Future Labs Corp-
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
 * FITNESS FOR A PARTICULAR PURPOSE AND NON INFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package    FLabsCode
 * @author    Carlos Arana
 * @copyright    Copyright (c) 2022 - 2022, FLabsCode
 * @license    http://opensource.org/licenses/MIT	MIT License
 * @link    https://flabscorpprods.com
 * @since    Version 1.0.0
 * @filesource
 */

/**
 * Utf8 Class
 *
 * Provides support for UTF-8 environments
 *
 * @package        CodeIgniter
 * @category    UTF-8
 * @author        EllisLab Dev Team (modified by carlos arana / future labs sac)
 */
class flcUtf8 {

    /**
     * Static for initialize UTF8_ENABLED
     *
     * Determines if UTF-8 support is to be enabled.
     * Need to be called before any utf8 or security stuff.
     *
     * @return    void
     * @throws \Exception if config cant be loaded
     */
    public static function initialize() {
        if (defined('PREG_BAD_UTF8_ERROR')                // PCRE must support UTF-8
            && (ICONV_ENABLED === true or MB_ENABLED === true)    // iconv or mbstring must be installed
            && strtoupper(FLC::get_instance()->get_config()->item('charset')) === 'UTF-8'    // Application charset must be UTF-8
        ) {
            define('UTF8_ENABLED', true);
            flcCommon::log_message('debug', 'flcUtf8->_construct - UTF-8 Support Enabled');
        } else {
            define('UTF8_ENABLED', false);
            flcCommon::log_message('debug', 'flcUtf8->_construct - UTF-8 Support Disabled');
        }

        flcCommon::log_message('info', 'flcUtf8->_construct - Utf8 Class Initialized');
    }

    // --------------------------------------------------------------------

    /**
     * Clean UTF-8 strings
     *
     * Ensures strings contain only valid UTF-8 characters.
     *
     * @param string $p_str String to clean
     *
     * @return    string
     */
    public static function clean_string(string $p_str) : string  {
        if (self::is_ascii($p_str) === false) {
            if (MB_ENABLED) {
                $p_str = mb_convert_encoding($p_str, 'UTF-8', 'UTF-8');
            } elseif (ICONV_ENABLED) {
                $p_str = @iconv('UTF-8', 'UTF-8//IGNORE', $p_str);
            }
        }

        return $p_str;
    }

    // --------------------------------------------------------------------

    /**
     * Remove ASCII control characters
     *
     * Removes all ASCII control characters except horizontal tabs,
     * line feeds, and carriage returns, as all others can cause
     * problems in XML.
     *
     * @param string $p_str String to clean
     *
     * @return    string
     */
    public static function safe_ascii_for_xml(string $p_str) : string {
        return flcCommon::remove_invisible_characters($p_str, false);
    }

    // --------------------------------------------------------------------

    /**
     * Convert to UTF-8
     *
     * Attempts to convert a string to UTF-8.
     *
     * @param string $p_str Input string
     * @param string $p_encoding Input encoding
     *
     * @return    string    $str encoded in UTF-8 or FALSE on failure
     */
    public static function convert_to_utf8(string $p_str, string $p_encoding) {
        if (MB_ENABLED) {
            return mb_convert_encoding($p_str, 'UTF-8', $p_encoding);
        } elseif (ICONV_ENABLED) {
            return @iconv($p_encoding, 'UTF-8', $p_str);
        }

        return false;
    }

    // --------------------------------------------------------------------

    /**
     * Is ASCII?
     *
     * Tests if a string is standard 7-bit ASCII or not.
     *
     * @param string $p_str String to check
     *
     * @return    bool
     */
    public static function is_ascii(string $p_str) : bool {
        return (preg_match('/[^\x00-\x7F]/S', $p_str) === 0);
    }



}
