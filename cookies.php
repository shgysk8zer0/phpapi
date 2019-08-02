<?php
/**
 * @author Chris Zuber <shgysk8zer0@gmail.com>
 * @package shgysk8zer0
 * @version 1.0.0
 * @copyright 2019, Chris Zuber
 * @license https://opensource.org/licenses/MIT MIT
 */
namespace shgysk8zer0\PHPAPI;

/**
 * Quick and easy way of setting/getting cookies
 *
 * @example
 * $cookies = new \shgysk8zer0\cookies();
 * $cookies->cookie_name = 'Value';
 * $cookie->existing_cookie //Returns value of $_COOKIES['existing-cookie']
 */
final class Cookies implements \Iterator, \JSONSerializable
{
	private static $_instance = null;

	/**
	 * Timestamp of when the cookie expires
	 * @var int
	 */
	private $_expires = 0;

	/**
	 * Path relative to DOCUMENT_ROOT/SERVER_NAME where the cookie is used
	 * @var string
	 */
	private $_path = '/';

	/**
	 * Name of server/domain the cookie is valid at
	 * @var string
	 */
	private $_domain = 'localhost';

	/**
	 * Use cookie only over HTTPS?
	 * @var bool
	 */
	private $_secure = false;

	/**
	 * Only send over HTTP requests (blocks access to JavaScript)
	 * @var bool
	 */
	private $_httponly = false;

	/**
	 * Initializes cookies class, setting all properties (similar to arguments)
	 *
	 * @param string  $domain   Whether or not to limit cookie to https connections
	 * @param string  $path     example.com/path would be /path
	 * @param mixed   $expires  Takes a variety of date formats, including timestamps
	 * @param bool    $secure   Setting to true prevents access by JavaScript, etc
	 * @param bool    $httponly Setting to true prevents access by JavaScript, etc
	 * @example $cookies = new cookies('Tomorrow', '/path', 'example.com', true, true);
	 */
	final public function __construct(
		URL  $url      = null,
		int  $expires  = 0,
		bool $secure   = null,
		bool $httponly = true
	)
	{
		if (is_null($url)) {
			$url = URL::getRequestUrl();
			$url->pathname = '/';
		}
		if (is_null($secure)) {
			$secure = array_key_exists('HTTPS', $_SERVER) and ! empty($_SERVER['HTTPS']);
		}

		$this->setExpires($expires);
		$this->setPath($url->pathname);
		$this->setSecure($secure);
		$this->setHttpOnly($httponly);
		$this->setDomain($url->hostname);

		if (is_null(static::$_instance)) {
			static::$_instance = $this;
		}

	}

	/**
	 * Magic setter for the class.
	 * Sets a cookie using only $name and $value. All
	 * other paramaters set in __construct
	 *
	 * @param string $key   Name of cookie to set
	 * @param string $value  Value to set it to
	 * @example $cookies->test = 'Works'
	 */
	public function __set(string $key, string $value): void
	{
		$this->_convertKey($key);
		$_COOKIE[$key] = $value;
		setcookie(
			$key,
			$value,
			$this->getExpires(),
			$this->getPath(),
			$this->getDomain(),
			$this->getSecure(),
			$this->getHttpOnly()
		);
	}

	/**
	 * Magic getter for the class
	 *
	 * Returns the requested cookie's value or false if not set
	 *
	 * @param string $key   Name of cookie to get
	 * @return mixed Value of requested cookie
	 * @example $cookies->test // returns 'Works'
	 */
	public function __get(string $key): string
	{
		$this->_convertKey($key);
		return isset($this->$key) ? $_COOKIE[$key] : null;
	}

	/**
	 * Checks if $_COOKIE[$key] exists
	 *
	 * @param string $key  Name of cookie to test if exists
	 * @return bool
	 * @example isset($cookies->$key) (true)
	 */
	public function __isset(string $key): bool
	{
		$this->_convertKey($key);
		return array_key_exists($key, $_COOKIE);
	}

	/**
	 * Completely destroys a cookie on server and client
	 *
	 * @param string $key  Name of cookie to remove
	 * @return void
	 * @example unset($cookies->$key)
	 */
	public function __unset(string $key): void
	{
		$this->_convertKey($key);
		if (isset($this->$key)) {
			unset($_COOKIE[$key]);
			setcookie(
				$key,
				null,
				-1,
				$this->getPath(),
				$this->getDomain(),
				$this->getSecure(),
				$this->getHttpOnly()
			);
		}
	}

	final public function __debugInfo(): array
	{
		return [
			'config' => [
				'domain'   => $this->getDomain(),
				'path'     => $this->getPath(),
				'expires'  => date_create()->setTimestamp($this->getExpires()),
				'secure'   => $this->getSecure(),
				'httponly' => $this->getHttpOnly(),
			],
			'cookie' => $_COOKIE,
		];
	}

	final public function jsonSerialize(): array
	{
		return $_COOKIE;
	}

	/**
	 * Gets the value @ $_iterator_position
	 *
	 * @param void
	 * @return mixed Whatever the current value is
	 */
	public function current()
	{
		return $_COOKIE[$this->key()];
	}

	/**
	 * Returns the original key (not $_iterator_position) at the current position
	 *
	 * @param void
	 * @return mixed  Probably a string, but could be an integer.
	 */
	public function key()
	{
		return key($_COOKIE);
	}

	/**
	 * Increment $_iterator_position
	 *
	 * @param void
	 * @return void
	 */
	public function next()
	{
		next($_COOKIE);
	}

	/**
	 * Reset $_iterator_position to 0
	 *
	 * @param void
	 * @return void
	 */
	public function rewind()
	{
		reset($_COOKIE);
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
		return array_keys($_COOKIE);
	}

	public function setSecure(bool $secure): void
	{
		$this->_secure = $secure;
	}

	public function setHttpOnly(bool $httponly): void
	{
		$this->_httponly = $httponly;
	}

	public function setDomain(string $domain): void
	{
		$this->_domain = $domain;
	}

	public function setPath(string $path): void
	{
		$this->_path = $path;
	}

	public function setExpires(int $expires): void
	{
		$this->_expires = $expires;
	}

	public function getSecure(): bool
	{
		return $this->_secure;
	}

	public function getHttpOnly(): bool
	{
		return $this->_httponly;
	}

	public function getDomain(): string
	{
		return $this->_domain;
	}

	public function getPath(): string
	{
		return $this->_path;
	}

	public function getExpires(): int
	{
		return $this->_expires;
	}

	public static function getInstance(): self
	{
		if (is_null(static::$_instance)) {
			static::$_instance = new self();
		}
		return static::$_instance;
	}

	/**
	 * Provides a single & consistent method to convert keys in magic methods
	 *
	 * @param string $key Reference to the key given.
	 * @return self
	 * @example $this->_convertKey($key);
	 */
	private function _convertKey(&$key): self
	{
		$key = str_replace('_', '-', $key);
		return $this;
	}
}
