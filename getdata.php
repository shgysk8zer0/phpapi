<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{InputDataInterface};
use \shgysk8zer0\PHPAPI\Traits\{Singleton, InputDataTrait};
use \JSONSerializable;
use \Iterator;

final class GetData implements JSONSerializable, Iterator, InputDataInterface
{
	use Singleton;
	use InputDataTrait;

	final public function __construct(array $data = null)
	{
		$this->_setInputData(isset($data) ? $data : $_GET);
	}
}
