<?php
namespace shgysk8zer0\PHPAPI;

final class URLSearchParams implements \JSONSerializable
{
	const PREFIX     = '';
	const SEPARATOR  = '&';
	const ENCODING   =  PHP_QUERY_RFC1738;
	private $_params = [];

	final public function __construct(string $query = '')
	{
		if ($query !== '') {
			parse_str(ltrim($query, '?'), $params);
			$this->_params = $params;
		}
	}

	final public function __toString(): string
	{
		return count($this->_params) === 0 ? '' : '?' . http_build_query($this->_params, self::PREFIX, self::SEPARATOR, self::ENCODING);
	}

	final public function __debugInfo(): array
	{
		return $this->_params;
	}

	final public function jsonSerialize(): array
	{
		return $this->_params;
	}

	final public function set(string $key, string $value)
	{
		$this->_params[$key] = $value;
	}

	final public function has(string $key): bool
	{
		return array_key_exists($key, $this->_params);
	}

	final public function get(string $key): string
	{
		if ($this->has($key)) {
			return $this->_params[$key];
		} else {
			return null;
		}
	}

	final public function delete(string $key)
	{
		unset($this->_params[$key]);
	}

	final public function keys(): array
	{
		return array_keys($this->_params);
	}

	final public function values(): array
	{
		return array_values($this->_params);
	}
}
