<?php
namespace shgysk8zer0\PHPAPI\Schema;

class Organization extends Thing
{
	const TYPE = 'Organization';

	protected function _setData(\StdClass $data)
	{
		$data->id = intval($data->id);
		if (isset($data->address)) {
			$data->address = new PostalAddress($data->address);
		}
		$this->_setDataObject($data);
	}
}
