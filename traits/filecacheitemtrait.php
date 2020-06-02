<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Interfaces\CacheItemInterface;

trait FileCacheItemTrait
{
	use CacheItemTrait;

	/**
	 * Returns the key for the current cache item.
	 *
	 * The key is loaded by the Implementing Library, but should be available to
	 * the higher level callers when needed.
	 *
	 * @return string
	 *   The key string for this cache item.
	 */
	abstract public function getKey(): string;

	/**
	 * Retrieves the value of the item from the cache associated with this object's key.
	 *
	 * The value returned must be identical to the value originally stored by set().
	 *
	 * If isHit() returns false, this method MUST return null. Note that null
	 * is a legitimate cached value, so the isHit() method SHOULD be used to
	 * differentiate between "null value was found" and "no value was found."
	 *
	 * @return mixed
	 *   The value corresponding to this cache item's key, or null if not found.
	 */
	final public function get():? object
	{
		if ($this->isHit()) {
			return unserialize(file_get_contents($this->getKey()));
		} else {
			return null;
		}
	}

	/**
	 * Confirms if the cache item lookup resulted in a cache hit.
	 *
	 * Note: This method MUST NOT have a race condition between calling isHit()
	 * and calling get().
	 *
	 * @return bool
	 *   True if the request resulted in a cache hit. False otherwise.
	 */
	final public function isHit(): bool
	{
		return file_exists($this->getKey());
	}

	/**
	 * Sets the value represented by this cache item.
	 *
	 * The $value argument may be any item that can be serialized by PHP,
	 * although the method of serialization is left up to the Implementing
	 * Library.
	 *
	 * @param mixed $value
	 *   The serializable value to be stored.
	 *
	 * @return static
	 *   The invoked object.
	 */
	final public function set(object $value): CacheItemInterface
	{
		file_put_contents($this->getKey(), serialize($value));
		return $this;
	}
}
