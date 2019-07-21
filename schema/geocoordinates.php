<?php
namespace shgysk8zer0\PHPAPI\Schema;

class GeoCoordinates extends \shgysk8zer0\PHPAPI\Schema\Abstracts\Intangible
{
	const TYPE = 'GeoCoordinates';
	const DEFAULT_DIST_UNITS = 'ft';

	protected function _setData(\StdClass $data)
	{
		$this->_setId($data->id);
		$this->_set('identifier', $data->identifier);

		$this->setLongitude($data->longitude);
		$this->setLatitude($data->latitude);

		if (isset($data->elevation)) {
			$this->setElevation($data->elevation);
		}

		if (isset($data->name)) {
			$this->setName($data->name);
		}
	}

	public function setLongitude(float $lng)
	{
		$this->_set('longitude', $lng);
	}

	public function setLatitude(float $lat)
	{
		$this->_set('latitude', $lat);
	}

	public function setElevation(int $elevation, string $units = self::DEFAULT_DIST_UNITS)
	{
		if ($units !== 'm') {
			switch($units) {
				case 'ft':
					$elevation *= 0.3048;
					break;
				default:
					throw new \InvalidArgumentException(sprintf('Unsupported units, "%s"', $units));
			}
		}
		$this->_set('elevation', intval($elevation));
	}
}
