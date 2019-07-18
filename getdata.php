<?php
namespace shgysk8zer0\PHPAPI;

final class GetData implements \JSONSerializable, \Iterator, Interfaces\InputData
{
	use Traits\Singleton;
	use Traits\InputData;

	final public function __construct(array $data = null)
	{
		$this->_setInputData(isset($data) ? $data : $_GET);
	}
}
