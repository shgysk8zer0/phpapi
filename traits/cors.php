<?php

namespace shgysk8zer0\PHPAPI\Traits;
use \shgysk8zer0\PHPAPI\Traits\{Headers};

trait CORS
{
	use Headers;

	final public static function allowCredentials(bool $allow = true)
	{
		if ($allow) {
			static::set('Access-Control-Allow-Credentials', 'true');
		} else {
			static::delete('Access-Control-Allow-Credentials');
		}
	}

	final public static function allowHeaders(string ...$headers)
	{
		static::set('Access-Control-Allow-Headers', join(', ', $headers));
	}

	final public static function allowMethods(string ...$methods)
	{
		array_walk($methods, function(string $method)
		{
			$method = strtoupper($method);
		});
		static::set('Access-Control-Allow-Methods', join(', ', $methods));
	}

	final public static function allowOrigin(string $origin = '*')
	{
		static::set('Access-Control-Allow-Origin', $origin);
	}

	final public static function exposeHeaders(string ...$headers)
	{
		static::set('Access-Control-Expose-Headers', join(', ', $headers));
	}

	final public static function maxAge(int $age)
	{
		static::set('Access-Control-Max-Age', "{$age}");
	}
}
