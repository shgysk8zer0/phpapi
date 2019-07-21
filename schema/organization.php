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

		$this->setName($data->name);

		if (isset($data->description)) {
			$this->setDescription($data->description);
		}

		if (isset($data->url)) {
			$this->setUrl($data->url);
		}

		if (isset($data->address)) {
			$this->setAddress(new PostalAddress($data->address));
		}

		if (isset($data->email)) {
			$this->setEmail($data->email);
		}

		if (isset($data->telephone)) {
			$this->setTelephone($data->telephone);
		}

		if (isset($data->faxNumber)) {
			$this->setFaxNumber($data->faxNumber);
		}

		if (isset($data->image)) {
			$this->setImage(new ImageObject($data->image));
		}

		if (isset($data->logo)) {
			$this->setLogo(new ImageObject($data->logo));
		}
	}
}
