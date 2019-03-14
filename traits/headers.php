<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};

trait Headers
{
	static private $_headers = [];

	final public static function set(string $key, string $value, bool $replace = true)
	{
		header("{$key}: {$value}", $replace);
	}

	final public static function append(string $key, string $value)
	{
		static::set($key, $value, false);
	}

	final public static function get(string $key): string
	{
		static::_getHeaders();
		return static::$_headers[strtolower($key)];
	}

	final public static function has(string $key): bool
	{
		static::_getHeaders();
		return array_key_exists(strtolower($key), static::$_headers);
	}

	final public static function delete(string $key)
	{
		header_remove($key);
	}
	final public static function redirect(string $url, bool $permenant = false)
	{
		if (! static::sent()) {
			static::set('Location', $url);
			static::status($permenant ? HTTP::PERMANENT_REDIRECT : HTTP::TEMPORARY_REDIRECT);
			exit();
		} else {
			trigger_error('Attempting to redirect when headers already sent');
		}
	}

	final public static function sent(): bool
	{
		return headers_sent();
	}

	final public static function status(int $code = HTTP::OK)
	{
		http_response_code($code);
	}

	final public static function contentType(string $content_type)
	{
		static::set('Content-Type', $content_type);
	}

	final protected static function _getHeaders()
	{
		if (! empty(static::$_headers)) {
			$headers = getallheaders();
			$keys = array_map('strtolower', array_keys($headers));
			$values = array_values($headers);
			static::$_headers = array_combine($keys, $values);
		}
	}
}
