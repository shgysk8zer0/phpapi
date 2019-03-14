<?php
/**
 * @author Chris Zuber <shgysk8zer0@gmail.com>
 * @package shgysk8zer0
 * @version 1.0.0
 * @copyright 2019, Chris Zuber
 * @license http://opensource.org/licenses/MIT MIT
 */
namespace shgysk8zer0\PHPAPI;

/**
 * Since this class is using $_SESSION for all data, there are few variables
 * There are several methods to make better use of $_SESSION, and it adds the
 * ability to chain. As $_SESSION is used for all storage, there is no pro or
 * con to using __construct vs ::load()
*/
final class Session implements \Iterator, \JSONSerializable
{
	private static $_instance = null;

	/**
	 * Creates new instance of session. $name is optional, and sets session_name
	 * if session has not been started
	 *
	 * @param string $site optional name for session
	 * @return void
	 */
	public function __construct(
		string $name     = 'session',
		int    $lifetime = 3600,
		string $domain   = null,
		string $path     = '/',
		bool   $secure   = null,
		bool   $httponly = true,
		string $samesite = 'strict'
	)
	{
		// Do not create new session of one has already been created
		if (! static::active()) {
			session_name($name);
			if (is_null($secure)) {
				$secure = array_key_exists('HTTPS', $_SERVER) and ! empty($_server['HTTPS']);
			}
			if (is_null($domain)) {
				$domain = URL::getRequestUrl()->hostname;
			}
			//Avoid trying to figure out cookie paramaters for CLI
			if (PHP_SAPI !== 'cli') {
				if (! array_key_exists($name, $_COOKIE)) {
					session_set_cookie_params(
						$lifetime,
						$path,
						$domain,
						$secure,
						$httponly
					);
				}
			}
			session_start();
			$cookie[session_name()] = session_id();
		}

		if (is_null(static::$_instance)) {
			static::$_instance = $this;
		}
	}

	/**
	 * The getter method for the class.
	 *
	 * @param string $key  Name of property to retrieve
	 * @return mixed       Its value
	 * @example "$session->key" Returns $value
	 */
	public function __get(string $key)
	{
		$key = $this->_getKey($key);
		return array_key_exists($key, $_SESSION) ? $_SESSION[$key] : null;
	}

	/**
	 * Setter method for the class.
	 *
	 * @param string $key   Name of property to set
	 * @param mixed $value  Value to set it to
	 * @return void
	 * @example "$session->key = $value"
	 */
	public function __set(string $key, $value)
	{
		$_SESSION[$this->_getKey($key)] = $value;
	}

	/**
	 * @param string $key  Name of property to check
	 * @return bool        Whether or not it is set
	 * @example "isset({$session->key})"
	 */
	public function __isset(string $key): bool
	{
		return array_key_exists($this->_getKey($key), $_SESSION);
	}

	/**
	 * Removes an index from the array.
	 *
	 * @param string $key  Name of property to unset/remove
	 * @return void
	 * @example "unset($session->key)"
	 */
	public function __unset(string $key)
	{
		unset($_SESSION[$this->_getKey($key)]);
	}

	public function jsonSerialize(): array
	{
		return $_SESSION;
	}

	/**
	 * Called whenever `var_dump` is called on class
	 * @return array Session data
	 */
	public function __debugInfo(): array
	{
		return [
			'params' => $this->getParams(),
			'data'   => $_SESSION,
		];
	}

	/**
	 * Gets the value @ $_iterator_position
	 *
	 * @param void
	 * @return mixed Whatever the current value is
	 */
	public function current()
	{
		return $_SESSION[$this->key()];
	}

	/**
	 * Returns the original key (not $_iterator_position) at the current position
	 *
	 * @param void
	 * @return mixed  Probably a string, but could be an integer.
	 */
	public function key()
	{
		return key($_SESSION);
	}

	/**
	 * Increment $_iterator_position
	 *
	 * @param void
	 * @return void
	 */
	public function next()
	{
		next($_SESSION);
	}

	/**
	 * Reset $_iterator_position to 0
	 *
	 * @param void
	 * @return void
	 */
	public function rewind()
	{
		reset($_SESSION);
	}

	/**
	 * Checks if data is set for current $_iterator_position
	 *
	 * @param void
	 * @return bool Whether or not there is data set at current position
	 */
	public function valid(): bool
	{
		return $this->key() !== null;
	}

	/**
	 * Lists all cookies by name
	 *
	 * @param void
	 * @return array
	 * @example $cookies->keys() (['test', ...])
	 * @deprecated
	 */
	public function keys(): array
	{
		return array_keys($_SESSION);
	}

	/**
	* Destroys $_SESSION and attempts to destroy the associated cookie
	*
	* @param void
	* @return void
	*/
	public function destroy()
	{
		$name = session_name();
		session_destroy();
		unset($_SESSION);

		if (array_key_exists($name, $_COOKIE)) {
			unset($_COOKIE[$name]);
			setcookie($name, null, -1);
		}
	}

	/**
	 * Clear $_SESSION. All data in $_SESSION is unset
	 *
	 * @param void
	 * @return self
	 * @example $session->restart()
	 */
	public function restart(): self
	{
		session_unset();
		return $this;
	}

	final public function getParams(): array
	{
		return session_get_cookie_params();
	}

	final public function getId(): string
	{
		return session_id();
	}

	final public static function status(): int
	{
		return session_status();
	}

	final public static function active(): bool
	{
		return static::status() === PHP_SESSION_ACTIVE;
	}

	final public static function disabled(): bool
	{
		return static::status() === PHP_SESSION_DISABLED;
	}


	final public static function getInstance(): self
	{
		if (is_null(static::$_instance)) {
			static::$_instance = new self();
		}
		return static::$_instance;
	}

	/**
	 * Converts array key for $_SESSION into something consistent
	 *
	 * @param string $key The original value
	 * @return string     The converted value
	 */
	private function _getKey(string $key): string
	{
		return strtolower(str_replace('_', '-', $key));
	}
}
