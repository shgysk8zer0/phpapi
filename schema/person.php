<?php
namespace shgysk8zer0\PHPAPI\Schema;

class Person extends Thing
{
	const TYPE = 'Person';

	protected function _setData(\StdClass $data)
	{
		$data->id = intval($data->id);
		if (isset($data->address)) {
			$data->address = new PostalAddress($data->address);
		}
		if (isset($data->worksFor)) {
			$data->worksFor = new Organization($data->worksFor);
		}
		$this->_setDataObject($data);
	}
}
