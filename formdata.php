<?php
namespace shgysk8zer0\PHPAPI;

final class FormData implements \JSONSerializable, \Iterator
{
	use Traits\Singleton;

	private static $_escape = true;
	private static $_data = [];

	final public function __construct()
	{
		if (array_key_exists('CONTENT_TYPE', $_SERVER)) {
			switch (strtolower($_SERVER['CONTENT_TYPE'])) {
				case 'application/json':
				case 'text/json':
					static::$_data = json_decode(file_get_contents('php://input'), true);
					break;
				case 'application/csp-report':
					$report = json_decode(file_get_contents('php://input'), true);
					if (array_key_exists('csp-report', $report)) {
						static::$_data = $report['csp-report'];
					}
					break;
				case 'text/plain':
				case 'application/text':
					static::$_data = ['text' => file_get_contents('php://input')];
					break;
				default:
					static::$_data = $_POST;
			}
		} else {
			static::$_data = $_POST;
		}

	}

	final public function get(string $key, bool $escape = true): string
	{
		if (! $this->has($key)) {
			return '';
		} elseif ($escape) {
			return htmlentities(static::$_data[$key]);
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
	public function next(): void
	{
		next(static::$_data);
	}

	/**
	 * Reset $_iterator_position to 0
	 *
	 * @param void
	 * @return void
	 */
	public function rewind(): void
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

	final public static function setEscape(bool $escape): void
	{
		static::$_escape = $escape;
	}
}
