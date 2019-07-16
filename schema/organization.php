<?php
namespace shgysk8zer0\PHPAPI\Schema;

class Organization extends Thing
{
	const TYPE = 'Organization';

	use Traits\Search;

	public function setAddress(PostalAddress $addr)
	{
		$this->_set('address', $addr);
	}

	public function setEmail(string $email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->_set('email', $email);
		} else {
			throw new \InvalidArgumentException(sprintf('"%s" is not a valid email', $email));
		}
	}

	public function setUrl(string $url)
	{
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			$this->_set('url', $url);
		} else {
			throw new \InvalidArgumentException(sprintf('"%s" is not a valid URL', $url));
		}
	}

	public function setTelephone(string $phone)
	{
		$this->_set('telephone', $phone);
	}

	public function setLogo(ImageObject $logo)
	{
		$this->_set('logo', $logo);
	}

	protected function _setData(\StdClass $data)
	{
		$data->id = intval($data->id);

		if (isset($data->address)) {
			$data->address = new PostalAddress($data->address);
		}

		if (isset($data->logo)) {
			$data->logo = new ImageObject($data->logo);
		}

		if (isset($data->image)) {
			$data->image = new ImageObject($data->image);
		}

		$this->_setDataObject($data);
	}
}
