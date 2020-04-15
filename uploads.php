<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{Singleton};
class Uploads extends \ArrayObject implements \JSONSerializable
{
	use Singleton;
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
}
