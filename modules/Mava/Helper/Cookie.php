<?php
class Mava_Helper_Cookie
{
    /**
     * Private constructor. Use statically.
     */
    private function __construct()
    {
    }

    /**
     * @param string $name Name of the cookie
     * @param string|false $value Value of the cookie, false to delete
     * @param integer $expiration Time stamp the cookie expires
     * @param boolean $httpOnly Whether the cookie should be available via HTTP only
     * @param boolean|null $secure Whether the cookie should be available via HTTPS only; if null, value is true if currently on HTTPS
     *
     * @return boolean True if set
     */
    protected static function _setCookieInternal($name, $value, $expiration = 0, $httpOnly = false, $secure = null)
    {
        if ($secure === null)
        {
            $secure = Mava_Application::$secure;
        }

        $cookieConfig = Mava_Application::getConfig('cookie');
        $path = $cookieConfig['path'];
        $domain = $cookieConfig['domain'];

        if ($value === false)
        {
            $expiration = Mava_Application::$time - 86400 * 365;
        }

        $name = $cookieConfig['prefix'] . $name;

        try
        {
            return setcookie($name, $value, $expiration, $path, $domain, $secure, $httpOnly);
        }
        catch (Exception $e)
        {
            return false; // possibly an error with the name... silencing may not be ideal, but it shouldn't usually happen
        }
    }

    /**
     * @param string $name Name of the cookie
     * @param string $value Value of the cookie
     * @param integer $lifetimeSecond The number of seconds the cookie should live from now. If 0, sets a session cookie.
     * @param boolean $httpOnly Whether the cookie should be available via HTTP only
     * @param boolean|null $secure Whether the cookie should be available via HTTPS only; if null, value is true if currently on HTTPS
     *
     * @return boolean True if set
     */
    public static function setCookie($name, $value, $lifetimeSecond = 0, $httpOnly = false, $secure = null)
    {
        $expiration = ($lifetimeSecond ? (Mava_Application::$time + $lifetimeSecond) : 0);
        return self::_setCookieInternal($name, $value, $expiration, $httpOnly, $secure);
    }

    /**
     * @param string $name Name of cookie
     * @param boolean $httpOnly Whether the cookie should be available via HTTP only
     * @param boolean|null $secure Whether the cookie should be available via HTTPS only; if null, value is true if currently on HTTPS
     *
     * @return boolean True if deleted
     */
    public static function deleteCookie($name, $httpOnly = false, $secure = null)
    {
        return self::_setCookieInternal($name, false, 0, $httpOnly, $secure);
    }

    /**
     * @param array $skip List of cookies to skip
     * @param array $flags List of flags to apply to individual cookies. [cookie name] => {httpOnly: true/false, secure: true/false/null}
     */
    public static function deleteAllCookies(array $skip = array(), array $flags = array())
    {
        if (empty($_COOKIE))
        {
            return;
        }

        $cookieConfig = Mava_Application::getConfig('cookie');
        $prefix = $cookieConfig['prefix'];
        foreach ($_COOKIE AS $cookie => $null)
        {
            if (strpos($cookie, $prefix) === 0)
            {
                $cookieStripped = substr($cookie, strlen($prefix));
                if (in_array($cookieStripped, $skip))
                {
                    continue;
                }

                $cookieSettings = array('httpOnly' => false, 'secure' => null);
                if (!empty($flags[$cookieStripped]))
                {
                    $cookieSettings = array_merge($cookieSettings, $flags[$cookieStripped]);
                }

                self::_setCookieInternal($cookieStripped, false, 0, $cookieSettings['httpOnly'], $cookieSettings['secure']);
            }
        }
    }

    /**
     * @param string $name Cookie name without prefix
     *
     * @return string|array|false False if cookie isn't found
     */
    public static function getCookie($name)
    {
        $cookieConfig = Mava_Application::getConfig('cookie');
        $name = $cookieConfig['prefix'] . $name;

        if (isset($_COOKIE[$name]))
        {
            return $_COOKIE[$name];
        }
        else
        {
            return false;
        }
    }

    /**
     * @param integer|string $id
     * @param string $name
     *
     * @return array Exploded cookie array
     */
    public static function clearIdFromCookie($id, $cookieName)
    {
        $cookie = self::getCookie($cookieName);
        if (!is_string($cookie) || $cookie === '')
        {
            return array();
        }

        $cookie = explode(',', $cookie);
        $position = array_search($id, $cookie);

        if ($position !== false)
        {
            unset($cookie[$position]);
            self::setCookie($cookieName, implode(',', $cookie));
        }

        return $cookie;
    }
}