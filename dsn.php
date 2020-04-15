<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerAwareTrait};
use \JsonSerializable;

final class DSN implements JSONSerializable, LoggerAwareInterface
{
	use LoggerAwareTrait;
	private $_type     = 'mysql';
	private $_database = null;
	private $_host     = 'localhost';
	private $_port     = 3306;
	private $_charset  = 'UTF8';
	private $_username = null;
	private $_password = null;

	final public function __toString(): string
	{
		$dsn     = [];
		$port    = $this->getPort();

		if (isset($this->_database)) {
			$dsn['dbname'] = $this->getDatabase();
		}

		$dsn['host'] = $this->getHost();
		$dsn['charset'] = $this->getCharset();
		if ($port !== 3306) {
			$dsn['port'] = "{$port}";
		}

		$keys   = array_keys($dsn);
		$values = array_values($dsn);

		$parts  = array_map(function(string $key, string $value): string
		{
			return "{$key}={$value}";
		}, $keys, $values);
		return "{$this->getType()}:" . join(';', $parts);
	}

	final public function __debugInfo(): array
	{
		return [
			'type'     => $this->_type,
			'database' => $this->_database,
			'host'     => $this->_host,
			'port'     => $this->_port,
			'charset'  => $this->_charset,
			'username' => $this->_username,
			'password' => $this->_password,
		];
	}

	final public function jsonSerialize(): array
	{
		$data = [];
		if (isset($this->_username)) {
			$data['username'] = $this->getUsername();
		}
		if (isset($this->_password)) {
			$data['password'] = $this->getPassword();
		}
		$data['type'] = $this->getType();
		$data['host'] = $this->getHost();
		$data['port'] = $this->getPort();
		if (isset($this->_database)) {
			$data['database'] = $this->getDatabase();
		}
		$data['charset'] = $this->getCharset();
		return $data;
	}

	final public function getCharset(): string
	{
		return $this->_charset;
	}

	final public function setCharset(string $charset)
	{
		$this->_charset = $charset;
	}

	final public function getDatabase(): string
	{
		return $this->_database;
	}

	final public function setDatabase(string $database)
	{
		$this->_database = $database;
	}

	final public function getHost(): string
	{
		return $this->_host;
	}

	final public function setHost(string $host)
	{
		$this->_host = $host;
	}

	final public function getPassword(): string
	{
		return $this->_password;
	}

	final public function setPassword(string $password)
	{
		$this->_password = $password;
	}

	final public function getPort(): int
	{
		return $this->_port;
	}

	final public function setPort(int $port)
	{
		$this->_port = $port;
	}

	final public function getType(): string
	{
		return $this->_type;
	}

	final public function setType(string $type)
	{
		$this->_type = $type;
	}

	final public function getUsername(): string
	{
		return $this->_username;
	}

	final public function setUsername(string $username)
	{
		$this->_username = $username;
	}

	final public function saveAs(string $filename): bool
	{
		return file_put_contents($filename, json_encode($this, JSON_PRETTY_PRINT));
	}

	final public static function loadFromObject(\stdClass $obj): self
	{
		$dsn = new self();
		if (isset($obj->database) and is_string($obj->database)) {
			$dsn->setDatabase($obj->database);
		}
		if (isset($obj->host) and is_string($obj->host)) {
			$dsn->setHost($obj->host);
		}
		if (isset($obj->type) and is_string($obj->type)) {
			$dsn->setType($obj->type);
		}
		if (isset($obj->charset) and is_string($obj->charset)) {
			$dsn->setCharset($obj->charset);
		}
		if (isset($obj->port) and is_numeric($obj->port)) {
			$dsn->setPort($obj->port);
		}
		if (isset($obj->username) and is_string($obj->username)) {
			$dsn->setUsername($obj->username);
		}
		if (isset($obj->password) and is_string($obj->password)) {
			$dsn->setPassword($obj->password);
		}
		return $dsn;
	}

	final public static function loadFromArray(array $arr): self
	{
		$dsn = new self();
		if (array_key_exists('database', $arr) and is_string($arr['database'])) {
			$dsn->setDatabase($arr['database']);
		}
		if (array_key_exists('host', $arr) and is_string($arr['host'])) {
			$dsn->setHost($arr['host']);
		}
		if (array_key_exists('type', $arr) and is_string($arr['type'])) {
			$dsn->setType($arr['type']);
		}
		if (array_key_exists('charset', $arr) and is_string($arr['charset'])) {
			$dsn->setCharset($arr['charset']);
		}
		if (array_key_exists('port', $arr) and is_numeric($arr['port'])) {
			$dsn->setPort($arr['port']);
		}
		if (array_key_exists('username', $arr)) {
			$dsn->setUsername($arr['username']);
		}
		if (array_key_exists('password', $arr)) {
			$dsn->setPassword($arr['password']);
		}
		return $dsn;
	}

	final public static function loadFromJSON(string $json): self
	{
		return static::loadFromObject(json_decode($json));
	}

	final public static function loadFromJSONFile(string $file): self
	{
		return static::loadFromJSON(file_get_contents($file));
	}

	final public static function loadFromURL(string $url): self
	{
		$dsn = new self();
		$data = parse_url($url);

		if (array_key_exists('scheme', $data)) {
			$dsn->setType($data['scheme']);
		}

		if (array_key_exists('host', $data)) {
			$dsn->setHost($data['host']);
		}

		if (array_key_exists('port', $data)) {
			$dsn->setPort($data['port']);
		}

		if (array_key_exists('path', $data)) {
			$dsn->setDatabase(substr($data['path'], 1));
		}

		if (array_key_exists('user', $data)) {
			$dsn->setUsername($data['user']);
		}

		if (array_key_exists('pass', $data)) {
			$dsn->setPassword($data['pass']);
		}

		return $dsn;
	}
}
