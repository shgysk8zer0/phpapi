<?php
namespace shgysk8zer0\PHPAPI;

final class GetData implements \JSONSerializable, \Iterator
{
	use Traits\Singleton;
	use Traits\InputData;

	final public function __construct()
	{
		static::_setInputData($_GET);
	}
}
