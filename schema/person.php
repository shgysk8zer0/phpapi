<?php
namespace shgysk8zer0\PHPAPI\Schema;

class Person extends Thing
{
	const TYPE = 'Person';

	protected function _setData(\StdClass $data)
	{
		$data->id = intval($data->id);
		if (isset($data->postalAddress)) {
			$data->postalAddress = new PostalAddress($data->postalAddress);
		}
		$this->_setDataObject($data);
	}
}
