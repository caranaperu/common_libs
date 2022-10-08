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

namespace framework\utils;

/**
 * Common string utilities class.
 */
class flcStrUtils {
    // --------------------------------------------------------------------

    /**
     * Byte-safe strlen()
     *
     * @param bool   $p_func_verride
     * @param string $p_str
     *
     * @return    int
     */
    public  static function strlen(bool $p_func_verride,string $p_str) : int {
        return ($p_func_verride) ? mb_strlen($p_str, '8bit') : strlen($p_str);
    }

    // --------------------------------------------------------------------

    /**
     * Byte-safe substr()
     *
     * @param bool     $p_func_override
     * @param string   $p_str
     * @param int      $p_start
     * @param int|null $p_length
     *
     * @return    string
     */
    public static function substr(bool $p_func_override,string $p_str, int $p_start, ?int $p_length = null) : string {
        if ($p_func_override) {
            // mb_substr($str, $start, null, '8bit') returns an empty
            // string on PHP 5.3
            isset($p_length) or $p_length = ($p_start >= 0 ? self::strlen(true,$p_str) - $p_start : -$p_start);

            return mb_substr($p_str, $p_start, $p_length, '8bit');
        }

        return isset($p_length) ? substr($p_str, $p_start, $p_length) : substr($p_str, $p_start);
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
    public static function valid_ip(string $p_ip, string $p_which = '') : bool {
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

}