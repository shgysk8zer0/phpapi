<?php

namespace shgysk8zer0\PHPAPI\Traits;
use \shgysk8zer0\PHPAPI\Traits\{Headers};

trait CORS
{
	use Headers;

	final public static function allowCredentials(bool $allow = true): void
	{
		if ($allow) {
			static::set('Access-Control-Allow-Credentials', 'true');
		} else {
			static::delete('Access-Control-Allow-Credentials');
		}
	}

	final public static function allowHeaders(string ...$headers): void
	{
		static::set('Access-Control-Allow-Headers', join(', ', $headers));
	}

	final public static function allowMethods(string ...$methods): void
	{
		array_walk($methods, function(string $method)
		{
			$method = strtoupper($method);
		});
		static::set('Access-Control-Allow-Methods', join(', ', $methods));
	}

	final public static function allowOrigin(string $origin = '*'): void
	{
		static::set('Access-Control-Allow-Origin', $origin);
	}

	final public static function exposeHeaders(string ...$headers): void
	{
		static::set('Access-Control-Expose-Headers', join(', ', $headers));
	}

	final public static function maxAge(int $age): void
	{
		static::set('Access-Control-Max-Age', "{$age}");
	}
}
