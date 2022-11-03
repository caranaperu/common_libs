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
 */

namespace framework\core\session\handler;

use Exception;
use framework\flcCommon;
use \SessionHandlerInterface;



/**
 * Base class for session handling
 */
abstract class flcBaseHandler implements \SessionHandlerInterface {

    /**
     * The Data fingerprint.
     *
     * @var string
     */
    protected string $finger_print;

    /**
     * Lock placeholder.
     *
     * @var mixed
     */
    protected $lock = false;

    /**
     * Cookie prefix
     *
     * The Config\Cookie::$prefix setting is completely ignored.
     * See https://codeigniter4.github.io/CodeIgniter4/libraries/sessions.html#session-preferences
     *
     * @var string
     */
    protected string $cookie_prefix = '';

    /**
     * Cookie domain
     *
     * @var string
     */
    protected string $cookie_domain = '';

    /**
     * Cookie path
     *
     * @var string
     */
    protected string $cookie_path = '/';

    /**
     * Cookie secure?
     *
     * @var bool
     */
    protected bool $cookieS_secure = false;

    /**
     * Cookie name to use
     *
     * @var string
     */
    protected string $cookie_name;

    /**
     * Match IP addresses for cookies?
     *
     * @var bool
     */
    protected bool $match_ip = false;

    /**
     * Current session ID
     *
     * @var string|null
     */
    protected ?string $session_id;

    /**
     * The 'save path' for the session
     * varies between
     *
     * @var array|string
     */
    protected $save_path;

    /**
     * User's IP address.
     *
     * @var string
     */
    protected string $ip_address;

    /**
     * Constructor
     *
     * @param string $p_p_ip_ddress
     *
     * @throws Exception
     */
    public function __construct(string $p_p_ip_ddress) {
        $config = flcCommon::get_config();

        // Session config stuff
        $this->cookie_domain = $config->item('cookie_domain');
        $this->cookie_path = $config->item('cookie_path');
        $this->cookieS_secure = $config->item('cookie_secure');

        $this->cookie_name = $config->item('sess_cookie_name');
        $this->match_ip = $config->item('sess_match_ip');
        $this->save_path = $config->item('sess_save_path');
        $this->ip_address = $p_p_ip_ddress;
    }

    // --------------------------------------------------------------------

    /**
     * Internal method to force removal of a cookie by the client
     * when session_destroy() is called.
     *
     * @return bool true if its destroyrd
     */
    protected function destroy_cookie(): bool {
        return setcookie($this->cookie_name, '', [
            'expires' => 1,
            'path' => $this->cookie_path,
            'domain' => $this->cookie_domain,
            'secure' => $this->cookieS_secure,
            'httponly' => true
        ]);
    }

    // --------------------------------------------------------------------

    /**
     * A dummy method allowing drivers with no locking functionality
     * (databases other than PostgreSQL and MySQL) to act as if they
     * do acquire a lock.
     *
     * @param string $p_session_id
     *
     * @return bool
     */
    protected function lock_session(string $p_session_id): bool {
        $this->lock = true;

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Releases the lock, if any.
     *
     * @return bool
     */
    protected function release_lock(): bool {
        $this->lock = false;

        return true;
    }

    // --------------------------------------------------------------------

    /**
     * Drivers other than the 'files' one don't (need to) use the
     * session.save_path INI setting, but that leads to confusing
     * error messages emitted by PHP when open() or write() fail,
     * as the message contains session.save_path ...
     *
     * To work around the problem, the drivers will call this method
     * so that the INI is set just in time for the error message to
     * be properly generated.
     *
     * @return bool
     */
    protected function fail(): bool {
        ini_set('session.save_path', $this->save_path);

        return false;
    }
    // --------------------------------------------------------------------
}

