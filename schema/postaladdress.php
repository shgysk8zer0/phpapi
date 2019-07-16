<?php
namespace shgysk8zer0\PHPAPI\Schema;

class PostalAddress extends Thing
{
	const TYPE = 'PostalAddress';

	public function setStreetAddress(string $address)
	{
		$this->_set('streetAddress', $address);
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
