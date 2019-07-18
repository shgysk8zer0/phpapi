<?php
namespace shgysk8zer0\PHPAPI;

final class UUID implements \JSONSerializable
{
	private $_uuid;

	final public function __construct()
	{
		$this->_uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand(0, 0xffff), mt_rand(0,0xffff),
			mt_rand(0, 0xffff),
			mt_rand(0, 0x0fff) | 0x4000,
			mt_rand(0, 0x3fff) | 0x8000,
			mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
		);
	}

	final public function __toString()
	{
		return $this->_uuid;
	}

	final public function jsonSerialize(): string
	{
		return $this->_uuid;
	}
}
