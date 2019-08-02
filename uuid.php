<?php
namespace shgysk8zer0\PHPAPI;

final class UUID implements \JSONSerializable
{
	private $_uuid;

	private const _FORMAT = '%04x%04x-%04x-%04x-%04x-%04x%04x%04x';

	final public function __construct()
	{
		$this->_uuid = static::generate();
	}

	final public function __toString(): string
	{
		return $this->_uuid;
	}

	final public function jsonSerialize(): string
	{
		return $this->_uuid;
	}

	final public static function generate(): string
	{
		return sprintf(self::_FORMAT,
			mt_rand(0, 0xffff), mt_rand(0,0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}
}
