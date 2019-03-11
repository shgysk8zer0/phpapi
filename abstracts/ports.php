<?php
namespace shgysk8zer0\PHPAPI\Abstracts;

abstract class Ports
{
	const PORTS = [
		'http:'  => 80,
		'https:' => 443,
	];

	final protected function _isDefaultPort(string $protocol, int $port): bool
	{
		return $this->_getDefaultPort($protocol) === $port;
	}

	final protected function _hasDefaultPort(string $protocol): bool
	{
		return array_key_exists(strtolower($protocol), self::PORTS);
	}

	final protected function _getDefaultPort(string $protocol): int
	{
		return self::PORTS[strtolower($protocol)] ?? 0;
	}
}
