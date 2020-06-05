<?php

namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{CacheInterface};

use \shgysk8zer0\PHPAPI\Traits\{CacheTrait};

use \DateInterval;


/**
 * No-op implementation of CacheInterface.
 *
 * This class does not provide a cache, but rather can serve as a default cache
 * for classes that allow setting custom caches, eliminating the need to check
 * if a cache is set.
 */
class NullCache implements CacheInterface
{
	use CacheTrait;

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
		return $default;
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
		return false;
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
		return false;
	}

	/**
	 * Wipes clean the entire cache's keys.
	 *
	 * @return bool True on success and false on failure.
	 */
	public function clear(): bool
	{
		return false;
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
		return false;
	}
}

