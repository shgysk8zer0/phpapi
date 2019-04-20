<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait MagicData
{
	final public static function setEscape(bool $escape)
	{
		static::$_escape = $escape;
	}

	final public function __invoke(...$args)
	{
		return $this->get(...$args);
	}

	final public function __get(string $key)
	{
		return $this->get($key);
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

	abstract public function get(
		string $key,
		bool   $escape  = true,
		string $default = null,
		string $charset = 'UTF-8'
	);

	abstract public function has(string ...$keys): bool;
}
