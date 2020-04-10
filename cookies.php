<?php
/**
 * @author Chris Zuber <shgysk8zer0@gmail.com>
 * @package shgysk8zer0
 * @version 1.0.0
 * @copyright 2020, Chris Zuber
 * @license https://opensource.org/licenses/MIT MIT
 */
namespace shgysk8zer0\PHPAPI;

use \DateTimeInterface;
use \DateTimeImmutable;
use \Iterator;
use \JSONSerializable;

/**
 * Quick and easy way of setting/getting cookies
 *
 * @example
 * $cookies = new \shgysk8zer0\PHPAPI\Cookies();
 * $cookies->foo = 'Value';
 * $cookie->{'existing-cookie'} //Returns value of $_COOKIES['existing-cookie']
 */
final class Cookies implements Iterator, JSONSerializable
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
	private $_domain = null;

	/**
	 * Asserts that a cookie must not be sent with cross-origin requests
	 * @var string
	 */
	private $_samesite = 'None';

	/**
	 * Use cookie only over HTTPS?
	 * @var bool
	 */
	private $_secure = true;

	/**
	 * Only send over HTTP requests (blocks access to JavaScript)
	 * @var bool
	 */
	private $_httponly = true;

	/**
	 * Initializes cookies class, setting all properties (similar to arguments)
	 *
	 * @param DateTimeInterface   $expires  Takes a variety of date formats, including timestamps
	 * @param string              $domain   Whether or not to limit cookie to https connections
	 * @param string              $path     example.com/path would be /path
	 * @param bool                $secure   Setting to true prevents usage in insecure contexts
	 * @param bool                $httponly Setting to true prevents access by JavaScript, etc
	 * @param string              $samesite Asserts that a cookie must not be sent with cross-origin requests
	 * @example $cookies = new Cookies(new DateTime('+6 hours'), '.example.com', '/path', true, true, 'Strict');
	 */
	final public function __construct(
		?DateTimeInterface $expires  = null,
		?string            $domain   = null,
		?string            $path     = null,
		?bool              $secure   = null,
		?bool              $httponly = true,
		?string            $samesite = 'None'
	)
	{
		if (isset($expires)) {
			$this->setExpirationDate($expires);
		}

		if (isset($secure)) {
			$this->setSecure($secure);
		}

		if (isset($httponly)) {
			$this->setHttpOnly($httponly);
		}

		if (isset($samesite)) {
			$this->setSameSite($samesite);
		}

		$this->setPath($path);
		$this->setDomain($domain);

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
	final public function __set(string $key, string $value): void
	{
		$this->set($key, $value, $this->getExpires(), $this->getPath(),
			$this->getDomain(), $this->getSecure(), $this->getHttpOnly(), $this->getSameSite());
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
	final public function __get(string $key):? string
	{
		return $this->get($key);
	}

	/**
	 * Checks if $_COOKIE[$key] exists
	 *
	 * @param string $key  Name of cookie to test if exists
	 * @return bool
	 * @example isset($cookies->$key) (true)
	 */
	final public function __isset(string $key): bool
	{
		return $this->has($key);
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
		$this->delete($key);
	}

	final public function set(
		string  $name,
		?string $value    = null,
		int     $expires  = 0,
		?string $path     = null,
		?string $domain   = null,
		bool    $secure   = false,
		bool    $httponly = false,
		string  $samesite = 'None'
	): bool
	{
		if (is_null($value)) {
			unset($_COOKIE[$name]);
		} else {
			$_COOKIE[$name] = $value;
		}

		if (version_compare(PHP_VERSION, '7.3', '>=')) {
			return setrawcookie($name, isset($value) ? rawurlencode($value) : null, array_filter([
				'path'     => $path,
				'domain'   => $domain,
				'expires'  => $expires,
				'secure'   => $secure,
				'httponly' => $httponly,
				'samesite' => $samesite,
			], function($val): bool
			{
				return isset($val);
			}));
		} else {
			return setrawcookie($name, rawurlencode($value), $expires, $path, $domain, $secure, $httponly);
		}
	}

	public function get(string $key):? string
	{
		return $this->has($key) ? $_COOKIE[$key] : null;
	}

	public function has(string... $keys): bool
	{
		$has = true;

		foreach ($keys as $key) {
			if (! array_key_exists($key, $_COOKIE)) {
				$has = false;
				break;
			}
		}

		return $has;
	}

	public function delete(string... $keys): void
	{
		foreach ($keys as $key) {
			if ($this->has($key)) {
				$this->set($key, null, 0, $this->getPath(), $this->getDomain(),
					$this->getSecure(), $this->getHttpOnly(), $this->getSameSite()
				);
			}
		}
	}

	public function clear(): void
	{
		$this->delete(...$this->keys());
	}

	final public function __debugInfo(): array
	{
		return [
			'config' => [
				'domain'   => $this->getDomain(),
				'path'     => $this->getPath(),
				'expires'  => $this->getExpirationDate(),
				'secure'   => $this->getSecure(),
				'httponly' => $this->getHttpOnly(),
				'samesite' => $this->getSameSite(),
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

	public function getSecure(): bool
	{
		return $this->_secure;
	}

	public function setSecure(bool $secure): void
	{
		$this->_secure = $secure;
	}

	public function getHttpOnly(): bool
	{
		return $this->_httponly;
	}

	public function setHttpOnly(bool $httponly): void
	{
		$this->_httponly = $httponly;
	}

	public function getDomain():? string
	{
		return $this->_domain;
	}

	public function setDomain(?string $domain): void
	{
		$this->_domain = $domain;
	}

	public function getPath(): string
	{
		return $this->_path;
	}

	public function setPath(?string $path): void
	{
		$this->_path = $path;
	}

	public function getExpires(): int
	{
		return $this->_expires;
	}

	public function setExpires(int $expires): void
	{
		$this->_expires = $expires;
	}

	final public function setExpirationDate(DateTimeInterface $val): void
	{
		$this->setExpires($val->getTimestamp());
	}

	final public function getExpirationDate():? DateTimeImmutable
	{
		if ($this->_expires > 0) {
			return new DateTimeImmutable(sprintf('@%d', $this->getExpires()));
		} else {
			return null;
		}
	}

	public function getSameSite(): string
	{
		return $this->_samesite;
	}

	final public function setSameSite(string $val): void
	{
		$this->_samesite = $val;
	}

	public static function getInstance(): self
	{
		if (is_null(static::$_instance)) {
			static::$_instance = new self();
		}

		return static::$_instance;
	}

	public function loadINI(string $fname, ?string $section = null):? bool
	{
		if (! file_exists($fname)) {
			return bool;
		} elseif (! $config = parse_ini_file($fname, isset($section))) {
			return bool;
		} elseif (isset($section) and ! array_key_exists($section, $config)) {
			return bool;
		} else {
			if (isset($section)) {
				$config = $config[$section];
			}

			if (array_key_exists('expires', $config)) {
				$this->setExpirationDate(new DateTimeImmutable($config['expires']));
			}

			if (array_key_exists('domain', $config)) {
				$this->setDomain($config['domain']);
			}

			if (array_key_exists('path', $config)) {
				$this->setPath($config['path']);
			}

			if (array_key_exists('secure', $config)) {
				$this->setSecure($config['secure']);
			}

			if (array_key_exists('httponly', $config)) {
				$this->setHttpOnly($config['httponly']);
			}

			if (array_key_exists('samesite', $config)) {
				$this->setSameSite($config['samesite']);
			}

			return true;
		}
	}
}
