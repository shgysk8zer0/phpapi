<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\{HTTPException, File};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \shgysk8zer0\PHPAPI\Traits\{Singleton};
use \JSONSerializable;
use \Iterator;

final class Files implements JSONSerializable,  Iterator
{
	use Singleton;
	private $_files = [];
	private $_path = '';

	final private function __construct()
	{
		foreach($_FILES as $key => $file) {
			if (is_array($file) and array_key_exists('error', $file) and $file['error'] !== UPLOAD_ERR_NO_FILE) {
				$this->_files[$key] = new File($key);
			}
		}
	}

	final public function __get(string $key)
	{
		if (isset($this->{$key})) {
			return $this->_files[$key];
		}
	}

	final public function __isset(string $key): bool
	{
		return array_key_exists($key, $this->_files);
	}

	final public function __debugInfo(): array
	{
		return $this->_files;
	}

	final public function jsonSerialize(): array
	{
		return $this->_files;
	}

	/**
	 * Gets the value @ $_iterator_position
	 *
	 * @param void
	 * @return mixed Whatever the current value is
	 */
	public function current()
	{
		return $this->_files[$this->key()];
	}

	/**
	 * Returns the original key (not $_iterator_position) at the current position
	 *
	 * @param void
	 * @return mixed  Probably a string, but could be an integer.
	 */
	public function key()
	{
		return key($this->_files);
	}

	/**
	 * Increment $_iterator_position
	 *
	 * @param void
	 * @return void
	 */
	public function next()
	{
		next($this->_files);
	}

	/**
	 * Reset $_iterator_position to 0
	 *
	 * @param void
	 * @return void
	 */
	public function rewind()
	{
		reset($this->_files);
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
		return array_keys($this->_files);
	}
}
