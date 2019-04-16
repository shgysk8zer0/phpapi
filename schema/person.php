<?php
namespace shgysk8zer0\PHPAPI\Schema;

class Person extends Thing
{
	const TYPE = 'Person';
	use Traits\Search;

	final public static function searchByFamilyName(string $name, int $limit = 10, int $offset = 0): array
	{
		return static::_simpleSearch('familyName', $name, $limit, $offset);
	}

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
