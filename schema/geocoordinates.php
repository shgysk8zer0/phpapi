<?php
namespace shgysk8zer0\PHPAPI\Schema;

class GeoCoordinates extends \shgysk8zer0\PHPAPI\Schema\Abstracts\Intangible
{
	public const TYPE = 'GeoCoordinates';

	public const DEFAULT_DIST_UNITS = 'ft';

	protected function _setData(object $data)
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

	public function distanceTo(self $coords): float
	{
		$dLat = deg2rad($coords->latitude - $this->latitude);
		$dLon = deg2rad($coords->longitude - $this->longitude);

		$a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($this->latitude)) * cos(deg2rad($this->latitude)) * sin($dLon/2) * sin($dLon/2);
		$c = 2 * asin(sqrt($a));
		// Multiply by Equatorial radius of Earth (in meters)
		return 6378137 * $c;
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
