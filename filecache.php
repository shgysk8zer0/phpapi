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
};

use \DateTimeImmutable;
use \DateInterval;

class FileCache implements CacheInterface, LoggerAwareInterface
{
	public const CACHE_DIR = './.cache';

	use LoggerAwareTrait;

	use CacheTrait;

	private $_directory = self::CACHE_DIR;

	private $_extension = 'json';

	final public function __construct(?string $dir = null, ?LoggerInterface $logger = null)
	{
		if (isset($dir)) {
			$this->_setDirectory($dir);
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
			$parsed = @json_decode(file_get_contents($this->_getPath($key)));

			 if (! is_string($parsed->serialized)) {
			 	return $default;
			 } elseif (isset($parsed->expires) and strtotime($parsed->expires) < time()) {
			 	// @TODO return default instead after `FileCache::has()` is fixed to detect expiration
			 	$this->delete($key);
			 	return unserialize($parsed->serialized) ?? $default;
			 } elseif (! $value = unserialize($parsed->serialized)) {
			 	return $default;
			 } else {
			 	return $value;
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
		if (isset($ttl)) {
			$date = new DateTimeImmutable();
			return file_put_contents($this->_getPath($key), json_encode([
				'expires'    => $date->add($ttl)->format(DateTimeImmutable::W3C),
				'serialized' => serialize($value),
			])) !== false;
		} else {
			return file_put_contents($this->_getPath($key), json_encode([
				'expires'    => null,
				'serialized' => serialize($value),
			])) !== false;
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
		return unlink($this->_getPath($key));
	}

	/**
	 * Wipes clean the entire cache's keys.
	 *
	 * @return bool True on success and false on failure.
	 */
	public function clear(): bool
	{
		$ret = true;

		foreach ($this->getEntries() as $file) {
			if (! unlink(sprintf('%s/%s', $this->_getDirectory(), $file))) {
				$ret = false;
				break;
			}
		}

		return $ret;
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
		if (file_exists($this->_getPath($key))) {
			// @TODO check for expiration
			return true;
		} else {
			return false;
		}
	}

	public function getEntries(): array
	{
		$ext = $this->_getExtension();
		$len = strlen($ext) + 1;

		return array_filter(scandir($this->_getDirectory()), function(string $path) use ($ext, $len): bool
		{
			return substr($path, -$len) === ".{$ext}";
		});
	}

	final private function _getDirectory(): string
	{
		return $this->_directory;
	}

	final private function _setDirectory(string $dir): void
	{
		$this->_directory = rtrim($dir, '/') . '/';
	}

	final private function _getExtension(): string
	{
		return $this->_extension;
	}

	final private function _setExtension(string $val): void
	{
		$this->_extension = ltrim($val, '.');
	}

	final protected function _getPath(string $file): string
	{
		return sprintf('%s/%s.%s', $this->_getDirectory(), md5($file), $this->_getExtension());
	}
}

