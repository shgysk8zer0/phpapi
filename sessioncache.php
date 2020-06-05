<?php

namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{
	LoggerAwareInterface,
	CacheInterface,
	LoggerInterface,
};

use \shgysk8zer0\PHPAPI\Traits\{
	CacheTrait,
	LoggerAwareTrait,
	PDOAwareTrait,
	PDOCacheTrait,
};

use \DateTimeImmutable;
use \DateInterval;
use \DateTimeZone;
use \StdClass;
use \PDO;

class SessionCache implements CacheInterface, LoggerAwareInterface
{
	use LoggerAwareTrait;
	use PDOAwareTrait;
	use PDOCacheTrait;
	use CacheTrait;

	private $_session_key = 'cache';

	final public function __construct(string $key = null, ?LoggerInterface $logger = null)
	{
		if (isset($key)) {
			$this->setSessionKey($key);
		}

		if (isset($logger)) {
			$this->setLogger($logger);
		} else {
			$this->setLogger(new NullLogger());
		}
	}

	/**
	 * Fetches a value from the cache.
	 *
	 * @param string $key	 The unique key of this item in the cache.
	 * @param mixed  $default Default value to return if the key does not exist.
	 *
	 * @return mixed The value of the item from the cache, or $default in case of cache miss.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function get(string $key, ?object $default = null):? object
	{
		if ($this->has($key)) {
			if ($value = @unserialize($_SESSION[$this->_getSessionKey()][$key]->value)) {
				return $value;
			} else {
				return $default;
			}
		} else {
			return $default;
		}
	}

	/**
	 * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
	 *
	 * @param string				 $key   The key of the item to store.
	 * @param mixed				  $value The value of the item to store. Must be serializable.
	 * @param null|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
	 *									  the driver supports TTL then the library may set a default value
	 *									  for it or let the driver take care of that.
	 *
	 * @return bool True on success and false on failure.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function set(string $key, object $value, ?DateInterval $ttl = null): bool
	{
		$this->logger->debug('Setting {key}', ['key' => $key]);

		if ($this->_started()) {
			$entry = new StdClass();

			if (isset($ttl)) {
				$now = new DateTimeImmutable();
				$entry->expires = $now->add($ttl);
			} else {
				$entry->expires = null;
			}

			$entry->value = serialize($value);
			$_SESSION[$this->_getSessionKey()][$key] = $entry;

			return true;
		} else {
			return false;
		}

	}

	/**
	 * Delete an item from the cache by its unique key.
	 *
	 * @param string $key The unique cache key of the item to delete.
	 *
	 * @return bool True if the item was successfully removed. False if there was an error.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function delete(string $key): bool
	{
		if ($this->_has($key)) {
			unset($_SESSION[$this->_getSessionKey()][$key]);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Wipes clean the entire cache's keys.
	 *
	 * @return bool True on success and false on failure.
	 */
	public function clear(): bool
	{
		// Cannot use `_started()` here as that would create an infinite loop
		if (session_status() === PHP_SESSION_ACTIVE) {
			$_SESSION[$this->_getSessionKey()] = [];
			return true;
		} else {
			return false;
		}
	}

	public function clearExpired(): bool
	{
		if ($this->_started()) {
			$now = new DateTimeImmutable();

			foreach ($_SESSION[$this->_getSessionKey()] as $key => $entry) {
				if (isset($entry->expires) and $entry->expires < $now) {
					unset($_SESSION[$this->getSessionKey()][$key]);
				}
			}

			return true;
		} else {
			return false;
		}
	}

	public function getEntries(): array
	{
		if ($this->_started()) {
			return array_map(function(object $entry): object
			{
				unset($entry->value);
				return $entry;
			}, $_SESSION[$this->_getSessionKey()]);
		} else {
			return [];
		}
	}

	/**
	 * Determines whether an item is present in the cache.
	 *
	 * NOTE: It is recommended that has() is only to be used for cache warming type purposes
	 * and not to be used within your live applications operations for get/set, as this method
	 * is subject to a race condition where your has() will return true and immediately after,
	 * another script can remove it, making the state of your app out of date.
	 *
	 * @param string $key The cache item key.
	 *
	 * @return bool
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function has(string $key): bool
	{
		if (! $this->_has($key)) {
			return false;
		} else {
			return $_SESSION[$this->_getSessionKey()][$key]->expires > new DateTimeImmutable();
		}
	}

	final public function setSessionKey(string $key): void
	{
		$this->_session_key = $key;
	}

	final protected function _getSessionKey(): string
	{
		return $this->_session_key;
	}

	final private function _started(): bool
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			if (! array_key_exists($this->_getSessionKey(), $_SESSION)) {
				$this->clear();
			}

			return true;
		} else {
			return false;
		}
	}

	final private function _has(string $key): bool
	{
		if (! $this->_started()) {
			return false;
		} elseif (! array_key_exists($this->_getSessionKey(), $_SESSION)) {
			return false;
		} elseif (! array_key_exists($key, $_SESSION[$this->_getSessionKey()])) {
			return false;
		} else {
			return true;
		}
	}
}

