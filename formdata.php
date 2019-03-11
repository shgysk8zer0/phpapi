<?php
namespace shgysk8zer0\PHPAPI;

final class FormData implements \JSONSerializable, \Iterator
{
	use Traits\Singleton;

	private static $_escape = true;

	final public function get(string $key, bool $escape = true): string
	{
		if (! $this->has($key)) {
			return '';
		} elseif ($escape) {
			return htmlentities($_POST[$key]);
		} else {
			return $_POST[$key];
		}
	}

	final public function has(string ...$keys): bool
	{
		$has = true;
		foreach ($keys as $key) {
			if (! array_key_exists($key, $_POST)) {
				$has = false;
				break;
			}
		}
		return $has;
	}

	final public function __invoke(string $key, bool $escape = true): string
	{
		return $this->get($key, $escape);
	}

	final public function __get(string $key): string
	{
		return $this->get($key, static::$_escape);
	}

	final public function __isset(string $key): bool
	{
		return $this->has($key);
	}

	final public function __debugInfo(): array
	{
		return $_POST;
	}

	final public function jsonSerialize(): array
	{
		return $_POST;
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
		return key($_POST);
	}

	/**
	 * Increment $_iterator_position
	 *
	 * @param void
	 * @return void
	 */
	public function next(): void
	{
		next($_POST);
	}

	/**
	 * Reset $_iterator_position to 0
	 *
	 * @param void
	 * @return void
	 */
	public function rewind(): void
	{
		reset($_POST);
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
		return array_keys($_POST);
	}

	final public static function setEscape(bool $escape): void
	{
		static::$_escape = $escape;
	}
}
