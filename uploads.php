<?php

namespace shgysk8zer0\PHPAPI;

class Uploads extends \ArrayObject implements \JSONSerializable
{
	private static $_instance = null;

	protected function __construct()
	{
		$keys = array_keys($_FILES);
		$values = array_map(function(string $key): UploadFile
		{
			return new UploadFile($key);
		}, $keys);

		parent::__construct(array_combine($keys, $values), self::ARRAY_AS_PROPS);
	}

	public function jsonSerialize(): array
	{
		return $this->getArrayCopy();
	}

	public static function getInstance(): self
	{
		if (is_null(static::$_instance)) {
			static::$_instance = new self();
		}
		return static::$_instance;
	}
}
