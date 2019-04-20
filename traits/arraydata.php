<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait ArrayData
{
	/**
	 * Gets the value @ $_iterator_position
	 *
	 * @param void
	 * @return mixed Whatever the current value is
	 */
	public function current()
	{
		return $this->get($this->key());
	}

	/**
	 * Returns the original key (not $_iterator_position) at the current position
	 *
	 * @param void
	 * @return mixed  Probably a string, but could be an integer.
	 */
	public function key()
	{
		return key(static::$_data);
	}

	/**
	 * Increment $_iterator_position
	 *
	 * @param void
	 * @return void
	 */
	public function next()
	{
		next(static::$_data);
	}

	/**
	 * Reset $_iterator_position to 0
	 *
	 * @param void
	 * @return void
	 */
	public function rewind()
	{
		reset(static::$_data);
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
		return array_keys(static::$_data);
	}

	abstract public function get(
		string $key,
		bool   $escape  = true,
		string $default = null,
		string $charset = 'UTF-8'
	);
}
