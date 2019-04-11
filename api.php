<?php

namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{CORS, Validate};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \shgysk8zer0\PHPAPI\{HTTPException, Headers, URL};
use \Exception;
use \StdClass;

class API implements \JSONSerializable
{
	use CORS;
	use Validate;

	const DEFAULT_METHODS = [
		'HEAD',
		'OPTIONS',
		'GET',
		'POST',
		'DELETE',
	];

	private $_callbacks = [];
	private $_url = null;

	final public function __construct(string $origin = '*')
	{
		static::allowOrigin($origin);
		$this->_url = URL::getRequestUrl();

		if ($origin !== '*' and $this->origin !== $origin) {
			throw new HTTPException('Origin not allowed', HTTP::FORBIDDEN);
		}

		$this->on('OPTIONS', function()
		{
			Headers::set('Allow', join(', ', $this->options));
		});

		$this->on('HEAD', function()
		{
			Headers::set('Allow', join(', ', $this->options));
			Headers::set('Content-Type', 'application/json');
		});
	}

	final public function __get(string $prop)
	{
		switch(strtolower($prop)) {
			case 'accept': return $_SERVER['HTTP_ACCEPT'] ?? '*/*';
			case 'contentlength': return $_SERVER['CONTENT_LENGTH'] ?? 0;
			case 'contenttype': return $_SERVER['CONTENT_TYPE'] ?? null;
			case 'cookies': return Cookies::getInstance();
			case 'dnt': return array_key_exists('HTTP_DNT', $_SERVER) and ! empty($_SERVER['HTTP_DNT']);
			case 'files': return Files::getInstance();
			case 'get': return GetData::getInstance();
			case 'headers': return getallheaders();
			case 'https': return $this->_url->protocol === 'https:';
			case 'method': return $_SERVER['REQUEST_METHOD'] ?? 'GET';
			case 'options': return array_keys($this->_callbacks);
			case 'origin': return $this->_url->origin;
			case 'post': return FormData::getInstance();
			case 'remoteaddr':
			case 'remoteaddress': return $_SERVER['REMOTE_ADDR'] ?? null;
			case 'remotehost': return $_SERVER['REMOTE_HOST'] ?? null;
			case 'referer':
			case 'referrer': return array_key_exists('HTTP_REFERER', $_SERVER) ? new URL($_SERVER['HTTP_REFERER']) : null;
			case 'requesturi':
			case 'requesturl': "{$this->_url}";
			case 'serveraddress': return $_SERVER['SERVER_ADDR'];
			case 'servername': return $_SERVER['SERVER_NAME'];
			case 'session': return Session::getInstance();
			case 'url': return $this->_url;
			case 'upgradeinsecurerequests': return array_key_exists('HTTP_UPGRADE_INSECURE_REQUESTS', $_SERVER)
				and ! empty($_SERVER['HTTP_UPGRADE_INSECURE_REQUESTS']);
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

	final public function __call(string $method, array $args = [])
	{
		array_unshift($args, $this);
		$method = strtoupper($method);

		if (array_key_exists($method, $this->_callbacks)) {
			foreach ($this->_callbacks[$method] as $callback) {
				call_user_func_array($callback, $args);
			}
		}
	}

	final public function __invoke()
	{
		$method = $this->method;
		static::allowMethods(...$this->options);

		$args = func_get_args();
		array_unshift($args, $this);

		if (array_key_exists($method, $this->_callbacks)) {
			foreach ($this->_callbacks[$method] as $callback) {
				call_user_func_array($callback, $args);
			}
		} else {
			static::set('Allow', join(', ', $this->options));

			throw new HTTPException("Unsupported Method: {$method}", HTTP::METHOD_NOT_ALLOWED);
		}
	}

	final public function __debugInfo(): array
	{
		return [
			'callbacks'               => $this->_callbacks,
			'method'                  => $this->method,
			'url'                     => $this->url,
			'get'                     => $this->get,
			'post'                    => $this->post,
			'headers'                 => $this->headers,
			'accept'                  => $this->accept,
			'referrer'                => $this->referrer,
			'userAgent'               => $this->useragent,
			'cookies'                 => $this->cookies,
			'files'                   => $this->files,
			'options'                 => $this->options,
			'DNT'                     => $this->dnt,
			'upgradeInsecureRequests' => $this->upgradeInsecureRequests,
			'remoteAddress'           => $this->remoteAddress,
		];
	}

	final public function jsonSerialize(): array
	{
		return [
			'method'                  => $this->method,
			'url'                     => $this->url,
			'get'                     => $this->get,
			'post'                    => $this->post,
			'headers'                 => $this->headers,
			'accept'                  => $this->accept,
			'referrer'                => $this->referrer,
			'userAgent'               => $this->useragent,
			'cookies'                 => $this->cookies,
			'files'                   => $this->files,
			'options'                 => $this->options,
			'DNT'                     => $this->dnt,
			'upgradeInsecureRequests' => $this->upgradeInsecureRequests,
			'remoteAddress'           => $this->remoteAddress,
		];
	}

	final public function on(string $method, callable $callback)
	{
		$method = strtoupper($method);

		if (! array_key_exists($method, $this->_callbacks)) {
			$this->_callbacks[$method] = [];
		}
		$this->_callbacks[$method][] = $callback;
	}

	final public function has(string ...$keys): bool
	{
		$valid = true;
		foreach ($keys as $key) {
			if (! array_key_exists($key, $_REQUEST)) {
				$valid = false;
				break;
			}
		}
		return $valid;
	}

	final public function hasFile(string $key): bool
	{
		return array_key_exists($key, $this->files);
	}

	final public function redirect(URL $url, bool $permenant = false)
	{
		Headers::redirect($url, $permenant);
	}
}
