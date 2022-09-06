<?php

namespace framework\utils;

class flcStrUtils {
    /**
     * Remove Invisible Characters
     *
     * This prevents sandwiching null characters
     * between ascii characters, like Javascript.
     *
     * @param string
     * @param bool
     *
     * @return    string
     */
    public static function remove_invisible_characters(string $str, bool $url_encoded = true): string {
        $non_displayables = [];

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/i';    // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i';    // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
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