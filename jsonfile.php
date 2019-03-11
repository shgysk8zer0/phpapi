<?php

namespace shgysk8zer0\PHPAPI;

final class JSONFile implements \JSONSerializable
{
	private $_data = [];

	final public function __construct(string $filename = null)
	{
		if (isset($filename)) {
			$this->_data = json_decode(file_get_contents($filename), true);
		}
	}

	final public function __isset(string $key): bool
	{
		return array_key_exists($key, $this->_data);
	}

	final public function __unset(string $key): void
	{
		unset($this->_data[$key]);
	}

	final public function __set(string $key, $value)
	{
		$this->_data[$key] = $value;
	}

	final public function __get(string $key)
	{
		return $this->_data[$key];
	}

	final public function __debug_info(): array
	{
		return $this->_data;
	}

	final public function jsonSerialize(): array
	{
		return $this->_data;
	}

	final public function save(string $filename, bool $pretty_print = true): bool
	{
		$content = json_encode($this->_data, $pretty_print ? JSON_PRETTY_PRINT : null);
		return file_put_contents($filename, $content);
	}
}