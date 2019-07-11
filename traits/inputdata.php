<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait InputData
{
	private static $_escape = true;

	protected static $_data = [];

	final protected static function _setInputData(array $inputs = [])
	{
		static::$_data = $inputs;
	}

	final public function get(
		string $key,
		bool   $escape  = true,
		string $default = null,
		string $charset = 'UTF-8'
	)
	{
		if (! $this->has($key)) {
			return $default;
		} elseif ($escape) {
			return htmlspecialchars(static::$_data[$key], ENT_COMPAT | ENT_HTML5, $charset);
		} else {
			return static::$_data[$key];
		}
	}

	final public function has(string ...$keys): bool
	{
		$has = true;

		foreach ($keys as $key) {
			if (! array_key_exists($key, static::$_data)) {
				$has = false;
				break;
			}
		}
		return $has;
	}

	final public function __invoke(string $key, bool $escape = true)
	{
		return $this->get($key, $escape);
	}

	final public function __get(string $key)
	{
		return $this->get($key, static::$_escape);
	}

	final public function __isset(string $key): bool
	{
		return $this->has($key);
	}

	final public function __debugInfo(): array
	{
		return static::$_data;
	}

	final public function jsonSerialize(): array
	{
		return static::$_data;
	}

	/**
	 * Gets the value @ $_iterator_position
	 *
	 * @param void
	 * @return mixed Whatever the current value is
	 */
	public function current()
	{
		return $this->get($this->key(), static::$_escape);
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

	final public static function setEscape(bool $escape)
	{
		static::$_escape = $escape;
	}
}
