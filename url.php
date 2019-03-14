<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{URLSearchParams};
use \shgysk8zer0\PHPAPI\Abstracts\{Ports};

class URL extends Ports implements \JSONSerializable
{
	private $_protocol = 'http:';
	private $_username = '';
	private $_password = '';
	private $_hostname = 'localhost';
	private $_port = null;
	private $_pathname = '/';
	private $_searchParams = null;
	private $_hash = '';

	final public function __construct(string $url, string $base = '')
	{
		// @TODO Handle relative URLs, such as "./" & "../"
		$parsed = array_merge(parse_url($base), parse_url($url));

		if (array_key_exists('scheme', $parsed)) {
			$this->_setProtocol("{$parsed['scheme']}:");
		}
		if (array_key_exists('user', $parsed)) {
			$this->_setUsername($parsed['user']);
		}
		if (array_key_exists('pass', $parsed)) {
			$this->_setPassword($parsed['pass']);
		}
		if (array_key_exists('host', $parsed)) {
			$this->_setHostname($parsed['host']);
		}
		if (array_key_exists('port', $parsed)) {
			$this->_setPort($parsed['port']);
		}
		if (array_key_exists('path', $parsed)) {
			$this->_setPathname($parsed['path']);
		}
		if (array_key_exists('query', $parsed)) {
			$this->_setSearch($parsed['query']);
		} else {
			$this->_searchParams = new URLSearchParams();
		}
		if (array_key_exists('fragment', $parsed)) {
			$this->_setHash($parsed['fragment']);
		}
	}

	final public function __toString(): string
	{
		return $this->href;
	}

	final public function __get(string $key)
	{
		switch($key) {
			case 'protocol':     return $this->_protocol;
			case 'username':     return $this->_username;
			case 'password':     return $this->_password;
			case 'hostname':     return $this->_hostname;
			case 'host':         return $this->_isDefaultPort($this->protocol, $this->port)
				? $this->_hostname
				: "{$this->_hostname}:{$this->_port}";
			case 'port':         return $this->_port ?? $this->_getDefaultPort($this->protocol);
			case 'pathname':     return $this->_pathname;
			case 'search':       return "{$this->_searchParams}";
			case 'searchParams': return $this->_searchParams;
			case 'hash':         return $this->_hash !== '' ? "#{$this->_hash}" : '';
			case 'href':         return $this->_getHref();
			case 'origin':       return "{$this->protocol}//{$this->host}";
			default: throw new \InvalidArgumentException("Undefined property: {$key}");
		}
	}

	final public function __set(string $key, $value)
	{
		switch($key) {
			case 'protocol':
				$this->_setProtocol($value);
				break;
			case 'username':
				$this->_setUsername($value);
				break;
			case 'password':
				$this->_setPassword($value);
				break;
			case 'hostname':
				$this->_setHostname($value);
				break;
			case 'port':
				$this->_setPort($value);
				break;
			case 'pathname':
				$this->_setPathname($value);
				break;
			case 'search':
				$this->_setSearch($value);
				break;
			case 'hash':
				$this->_setHash($value);
				break;
			default: throw new \InvalidArgumentException("Undefined property: {$key}");
		}
	}

	final public function __unset(string $key)
	{
		switch($key) {
			case 'search':
				$this->search = '';
				break;
			case 'hash':
				$this->hash = '';
				break;
			case 'pathname':
				$this->pathname = '/';
				break;
			case 'username':
				$this->username = '';
				break;
			case 'password':
				$this->password = '';
				break;
			case 'port':
				if (array_key_exists($this->protocol, self::PORTS)) {
					$this->port = self::PORTS[$this->protocol];
				}
				break;
		}
	}

	final public function __debugInfo(): array
	{
		return [
			'protocol'     => $this->_protocol,
			'username'     => $this->_username,
			'password'     => $this->_password,
			'hostname'     => $this->_hostname,
			'port'         => $this->_port,
			'pathname'     => $this->_pathname,
			'searchParams' => $this->_searchParams,
			'hash'         => $this->_hash,
		];
	}

	final public function jsonSerialize(): array
	{
		return [
			'protocol'     => $this->protocol,
			'username'     => $this->username,
			'password'     => $this->password,
			'hostname'     => $this->hostname,
			'host'         => $this->host,
			'port'         => $this->port,
			'pathname'     => $this->pathname,
			'search'       => $this->search,
			'searchParams' => $this->searchParams,
			'hash'         => $this->hash,
			'href'         => $this->href,
			'origin'       => $this->origin,
		];
	}

	final private function _setProtocol(string $protocol)
	{
		$this->_protocol = $protocol;
	}

	final private function _setUsername(string $username)
	{
		$this->_username = $username;
	}

	final private function _setPassword(string $password)
	{
		$this->_password = $password;
	}

	final private function _setHostname(string $hostname)
	{
		$this->_hostname = $hostname;
	}

	final private function _setPort(int $port)
	{
		$this->_port = $port;
	}

	final private function _setPathname(string $pathname)
	{
		$this->_pathname = '/' . ltrim($pathname, '/');
	}

	final private function _setSearch(string $query)
	{
		$this->_setSearchParams(new URLSearchParams($query));
	}

	final private function _setSearchParams(URLSearchParams $params)
	{
		$this->_searchParams = $params;
	}

	final private function _setHash(string $hash)
	{
		$this->_hash = ltrim($hash, '#');
	}

	final private function _getHref(): string
	{
		$url = "{$this->protocol}//";
		if ($this->_username !== '') {
			$url .= urlencode($this->_username);
			if ($this->_password !== '') {
				$url .= ':' . urlencode($this->_password);
			}
			$url .= '@';
		}
		return $url . $this->host . $this->pathname . $this->search . $this->hash;
	}

	final public static function getRequestUrl(): self
	{
		$url = (array_key_exists('HTTPS', $_SERVER) and ! empty($_SERVER['HTTPS']))
			? 'https://'
			: 'http://';
		if (array_key_exists('PHP_AUTH_USER', $_SERVER)) {
			$url .= urlencode($_SERVER['PHP_AUTH_USER']);
			if (array_key_exists('PHP_AUTH_PW', $_SERVER)) {
				$url .= sprintf(':%s@', urlencode($_SERVER['PHP_AUTH_PW']));
			} else {
				$url .= '@';
			}
		}
		$url .= $_SERVER['HTTP_HOST'] ?? 'localhost';
		$url .= $_SERVER['REQUEST_URI'] ?? '/';
		return new URL($url);
	}
}
