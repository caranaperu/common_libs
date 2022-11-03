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


/**
 * Expected behavior of a session container used with CodeIgniter.
 */
interface flcSessionInterface {
    /**
     * Regenerates the session ID.
     *
     * @param bool $p_destroy Should old session data be destroyed?
     */
    public function regenerate(bool $p_destroy = false);

    /**
     * Destroys the current session.
     */
    public function destroy();

    /**
     * Sets user data into the session.
     *
     * If $data is a string, then it is interpreted as a session property
     * key, and  $value is expected to be non-null.
     *
     * If $data is an array, it is expected to be an array of key/value pairs
     * to be set as session properties.
     *
     * @param array|string $p_data Property name or associative array of properties
     * @param mixed        $p_value Property value if single key provided
     */
    public function set($p_data, $p_value = null);

    /**
     * Get user data that has been set in the session.
     *
     * If the property exists as "normal", returns it.
     * Otherwise, returns an array of any temp or flash data values with the
     * property key.
     *
     * Replaces the legacy method $session->userdata();
     *
     * @param string|null $p_key Identifier of the session property to retrieve
     *
     * @return mixed The property value(s)
     */
    public function get(?string $p_key = null);

    /**
     * Returns whether an index exists in the session array.
     *
     * @param string $p_key Identifier of the session property we are interested in.
     */
    public function has(string $p_key): bool;

    /**
     * Remove one or more session properties.
     *
     * If $key is an array, it is interpreted as an array of string property
     * identifiers to remove. Otherwise, it is interpreted as the identifier
     * of a specific session property to remove.
     *
     * @param array|string $p_key Identifier of the session property or properties to remove.
     */
    public function remove( $p_key);


}
