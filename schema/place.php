<?php
namespace shgysk8zer0\PHPAPI\Schema;

class Place extends Thing
{
	const TYPE = 'Place';

	protected function _setData(\StdClass $data)
	{
		$this->_setId($data->id);
		$this->_set('identifier', $data->identifier);
		if (isset($data->name)) {
			$this->setName($data->name);
		}

		if (isset($data->description)) {
		}

		if (isset($data->address)) {
			$this->setAddress(new PostalAddress($data->address));
		}

		if (isset($data->geo)) {
			$this->setGeo(new GeoCoordinates($data->geo));
		}

		if (isset($data->image)) {
			$this->setImage(new ImageObject($data->image));
		}

		$this->setPublicAccess($data->publicAccess === '1');
	}

	public function setAddress(PostalAddress $address)
	{
		$this->_set('address', $address);
	}

	public function setGeo(GeoCoordinates $geo)
	{
		$this->_set('geo', $geo);
	}

	public function setPublicAccess(bool $access)
	{
		$this->_set('publicAcces', $access);
	}

	public function setImage(ImageObject $img)
	{
		$this->_set('image', $img);
	}
}
