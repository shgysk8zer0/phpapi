<?php

namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{CORS};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \shgysk8zer0\PHPAPI\{HTTPException};

final class API
{
	final public function __construct(string $origin = '*', array $methods = ['POST', 'OPTIONS', 'HEAD'])
	{
		static::allowOrigin($origin);
		static::allowMethods(...$methods);

		if ($origin !== '*' and $this->origin !== $origin) {
			throw new HTTPException('Origin not allowed', HTTP::FORBIDDEN);
		}

		if (! in_array($this->method, $methods)) {
			static::allow(...$methods);

			throw new HTTPException(
				sprintf('Allowed methods: %s', join(', ', $methods)),
				HTTP::METHOD_NOT_ALLOWED
			);
		}
	}

	final public function __get(string $prop)
	{
		switch(strtolower($prop)) {
			case 'accept': return $_SERVER['HTTP_ACCEPT'] ?? '*/*';
			case 'contentlength': return $_SERVER['CONTENT_LENGTH'] ?? 0;
			case 'contenttype': return $_SERVER['CONTENT_TYPE'] ?? null;
			case 'https': return array_key_exists('HTTPS', $_SERVER) and $_SERVER['HTTPS'] !== 'off';
			case 'method': return $_SERVER['REQUEST_METHOD'] ?? null;
			case 'origin': return $_SERVER['HTTP_ORIGIN'] ?? null;
			case 'remoteaddr':
			case 'remoteaddress': return $_SERVER['REMOTE_ADDR'] ?? null;
			case 'remotehost': return $_SERVER['REMOTE_HOST'] ?? null;
			case 'referer':
			case 'referrer': return $_SERVER['HTTP_REFERER'] ?? null;
			case 'requesturi':
			case 'requesturl': return $_SERVER['REQUEST_URI'] ?? null;
			case 'serveraddress': return $_SERVER['SERVER_ADDR'];
			case 'servername': return $_SERVER['SERVER_NAME'];
			case 'useragent': return $_SERVER['HTTP_USER_AGENT'] ?? null;
			default: throw new \Exception(sprintf('Unknown property: %s', $prop));
		}
	}

	final public function __set(string $prop, $value)
	{
		switch(strtolower($prop)) {
			case 'contenttype':
				Headers::set('Content-Type', $value);
				break;
			case 'status':
				Headers::status($value);
				break;
			default: throw new \Exception(sprintf('Unknown property: %s', $prop));
		}
	}

	final public function on(string $method, callable $callback)
	{
		if (strtoupper($method) === $this->method) {
			call_user_func($callback);
		}
	}
}
