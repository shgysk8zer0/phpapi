<?php
namespace shgysk8zer0\PHPAPI\Schema;

class PostalAddress extends Thing
{
	public const TYPE = 'PostalAddress';

	protected function _setData(object $data)
	{
		$this->_setId($data->id);
		$this->_set('identifier', $data->identifier);
		if (isset($data->streetAddress)) {
			$this->setStreetAddress($data->streetAddress);
		}

		if (isset($data->postOfficeBoxNumber)) {
			$this->setPostOfficeBoxNumber($data->postOfficeBoxNumber);
		}

		if (isset($data->addressLocality)) {
			$this->setAddressLocality($data->addressLocality);
		}

		if (isset($data->addressRegion)) {
			$this->setAddressRegion($data->addressRegion);
		}

		if (isset($data->postalCode)) {
			$this->setPostalCode($data->postalCode);
		}

		if (isset($data->addressCountry)) {
			$this->setAddressCountry($data->addressCountry);
		}
	}

	public function setStreetAddress(string $address)
	{
		$this->_set('streetAddress', $address);
	}

	public function setPostOfficeBoxNumber(int $po_box)
	{
		$this->_set('postOfficeBoxNumber', $po_box);
	}

	public function setAddressLocality(string $city)
	{
		$this->_set('addressLocality', $city);
	}

	public function setAddressRegion(string $region)
	{
		$this->_set('addressRegion', $region);
	}

	public function setAddressCountry(string $country)
	{
		$this->_set('addressCountry', $country);
	}

	public function setPostalCode(int $postal_code)
	{
		$this->_set('postalCode', $postal_code);
	}
}
