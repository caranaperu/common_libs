<?php
/**
 * This file is part of Future Labs Code 1 framework.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * Based in codeigniter , all kudos for his authors
 *
 * @author Modified by Carlos Arana Reategui.
 *
 */

namespace framework\core;

use Exception;
use framework\flcCommon;

require_once dirname(__FILE__).'/../flcCommon.php';
require_once dirname(__FILE__).'/../utils/flcStrUtils.php';

/**
 * Utf8 Class
 *
 * Provides support for UTF-8 environments
 *
 */
class flcUtf8 {

    /**
     * Static for initialize UTF8_ENABLED
     *
     * Determines if UTF-8 support is to be enabled.
     * Need to be called before any utf8 or security stuff.
     *
     * @return    void
     * @throws Exception if config cant be loaded
     */
    public static function initialize() {
        if (defined('PREG_BAD_UTF8_ERROR')                // PCRE must support UTF-8
            && (ICONV_ENABLED === true or MB_ENABLED === true)    // iconv or mbstring must be installed
            && strtoupper(flcCommon::get_config()->item('charset')) === 'UTF-8'    // Application charset must be UTF-8
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
