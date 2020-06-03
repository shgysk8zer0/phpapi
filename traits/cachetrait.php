<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \DateInterval;

trait CacheTrait
{
	/**
	 * Obtains multiple cache items by their unique keys.
	 *
	 * @param iterable $keys	A list of keys that can obtained in a single operation.
	 * @param mixed	$default Default value to return for keys that do not exist.
	 *
	 * @return iterable A list of key => value pairs. Cache keys that do not exist or are stale will have $default as value.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if $keys is neither an array nor a Traversable,
	 *   or if any of the $keys are not a legal value.
	 */
	public function getMultiple(iterable $keys, ?object $default = null): iterable
	{
		$ret = [];

		foreach ($keys as $key) {
			$ret[$key] = $this->get($key, $default);
		}

		return $ret;
	}

	/**
	 * Persists a set of key => value pairs in the cache, with an optional TTL.
	 *
	 * @param iterable			   $values A list of key => value pairs for a multiple-set operation.
	 * @param null|\DateInterval $ttl	Optional. The TTL value of this item. If no value is sent and
	 *									   the driver supports TTL then the library may set a default value
	 *									   for it or let the driver take care of that.
	 *
	 * @return bool True on success and false on failure.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if $values is neither an array nor a Traversable,
	 *   or if any of the $values are not a legal value.
	 */
	public function setMultiple(iterable $values, ?DateInterval $ttl = null): bool
	{
		$ret = true;

		foreach ($values as $key => $value) {
			if (! $this->set($key, $value, $tty)) {
				$ret = false;
				break;
			}
		}

		return $ret;
	}

	/**
	 * Deletes multiple cache items in a single operation.
	 *
	 * @param iterable $keys A list of string-based keys to be deleted.
	 *
	 * @return bool True if the items were successfully removed. False if there was an error.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if $keys is neither an array nor a Traversable,
	 *   or if any of the $keys are not a legal value.
	 */
	public function deleteMultiple(iterable $keys): bool
	{
		$ret = true;

		foreach ($keys as $key) {
			if (! $this->delete($key)) {
				$ret = false;
				break;
			}
		}

		return $ret;
	}

	abstract public function get(string $key, ?object $default = null):? object;

	abstract public function set(string $key, object $value, ?DateInterval $ttl = null): bool;

	abstract public function delete(string $key): bool;
}
