<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

interface InputDataInterface
{
	public function get(
		string $key,
		bool   $escape  = true,
		string $default = null,
		string $charset = 'UTF-8'
	);

	public function has(string ...$keys): bool;

	public function __invoke(...$args);

	public function __get(string $key);

	public function __isset(string $key): bool;

	public function __debugInfo(): array;

	public function jsonSerialize(): array;

	public function toObject():? object;

	/**
	 * Gets the value @ $_iterator_position
	 *
	 * @param void
	 * @return mixed Whatever the current value is
	 */
	public function current();

	/**
	 * Returns the original key (not $_iterator_position) at the current position
	 *
	 * @param void
	 * @return mixed  Probably a string, but could be an integer.
	 */
	public function key();

	/**
	 * Increment $_iterator_position
	 *
	 * @param void
	 * @return void
	 */
	public function next();

	/**
	 * Reset $_iterator_position to 0
	 *
	 * @param void
	 * @return void
	 */
	public function rewind();

	/**
	 * Checks if data is set for current $_iterator_position
	 *
	 * @param void
	 * @return bool Whether or not there is data set at current position
	 */
	public function valid(): bool;

	/**
	 * Lists all cookies by name
	 *
	 * @param void
	 * @return array
	 * @example $cookies->keys() (['test', ...])
	 * @deprecated
	 */
	public function keys(): array;

	public static function setEscape(bool $escape);
}
