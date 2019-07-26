<?php
namespace shgysk8zer0\PHPAPI\Schema;

use \shgysk8zer0\PHPAPI\Interfaces\{InputData};
use \shgysk8zer0\PHPAPI\Schema\{PostalAddress};
use \DateTime;

class Person extends Thing
{
	const TYPE = 'Person';

	const QUERY = 'SELECT `givenName`,
		`Person`.`additionalName`,
		`Person`.`familyName`,
		`Person`.`id`,
		`Person`.`birthDate`,
		`Person`.`identifier`,
		`Person`.`email`,
		`Person`.`telephone`,
		`Person`.`jobTitle`,
		`PostalAddress`.`identifier` AS `addrUUID`,
		`PostalAddress`.`streetAddress`,
		`PostalAddress`.`postOfficeBoxNumber`,
		`PostalAddress`.`addressLocality`,
		`PostalAddress`.`addressRegion`,
		`PostalAddress`.`postalCode`,
		`PostalAddress`.`addressCountry`,
		`ImageObject`.`identifier` AS `imgUUID`,
		`ImageObject`.`url` AS `imgUrl`,
		`ImageObject`.`width`,
		`ImageObject`.`height`,
		`ImageObject`.`encodingFormat`,
		`ImageObject`.`isFamilyFriendly`,
		`Organization`.`name` AS `worksFor`,
		`Organization`.`identifier` AS `orgUUID`,
		`Organization`.`telephone` AS `orgPhone`,
		`Organization`.`email` AS `orgEmail`,
		`Organization`.`url` AS `orgUrl`,
		`Organization`.`image` AS `orgImg`,
		`Organization`.`logo` AS `orgLogo`,
		`Organization`.`address` AS `orgAddr`
		FROM `%s`
		LEFT OUTER JOIN `PostalAddress` ON `Person`.`address` = `PostalAddress`.`id`
		LEFT OUTER JOIN `ImageObject` ON `Person`.`address` = `ImageObject`.`id`
		LEFT OUTER JOIN `Organization` ON `Person`.`worksFor` = `Organization`.`id`
		WHERE `Person`.`%s` = :val
		LIMIT 1;';

	use Traits\Search;

	final public static function searchByFamilyName(string $name, int $limit = 10, int $offset = 0): array
	{
		return static::_simpleSearch('familyName', $name, $limit, $offset);
	}

	public static function create(InputData $input): Thing
	{
		$person = new self();
		$this->_set('identifier', $data->identifier);
		$this->_setId($data->id);
		$person->setName($input->get('givenName'));

		if ($input->has('additionalName')) {
			$person->setAdditionalName($input->get('additionalName'));
		}

		$person->setFamilyName($input->get('familyName'));

		if ($input->has('email')) {
			$person->setEmail($input->get('email'));
		}

		if ($input->has('telephone')) {
			$person->setTelephone($input->get('telephone'));
		}

		if ($input->has('birthDate')) {
			$person->set('birthDate', new DateTime($input->get('birthDate')));
		}

		return $person;
	}

	protected function _setData(\StdClass $data)
	{
		$this->_setId($data->id);
		$this->setGivenName($data->givenName);

		if (isset($data->additionalName)) {
			$this->setAdditionalName($data->additionalName);
		}

		$this->setFamilyName($data->familyName);

		if (isset($data->birthDate)) {
			$this->setBirthDate(new DateTime($data->birthDate));
		}

		if (isset($data->telephone)) {
			$this->setTelephone($data->telephone);
		}

		if (isset($data->email)) {
			$this->setEmail($data->email);
		}

		if (isset($data->imgUrl)) {
			$this->setImage(new ImageObject());
			$this->image->setUrl($data->imgUrl);

			if (isset($data->height, $data->width)) {
				$this->image->setHeight($data->height);
				$this->image->setWidth($data->width);
			}

			$this->image->setIsFamilyFriendly($data->isFamilyFriendly === '1');

			if (isset($data->encodingFormat)) {
				$this->image->setEncodingFormat($data->encodingFormat);
			}
		}

		if (isset($data->addressLocality, $data->addressRegion)) {
			$this->setAddress(new PostalAddress());
			if (isset($data->streetAddress)) {
				$this->address->setStreetAddress($data->streetAddress);
			}
			if (isset($data->postOfficeBoxNumber)) {
				$this->address->setPostOfficeBoxNumber($data->postOfficeBoxNumber);
			}
			$this->address->setAddressLocality($data->addressLocality);
			$this->address->setAddressRegion($data->addressRegion);
			if (isset($data->postalCode)) {
				$this->address->setPostalCode($data->postalCode);
			}
			if (isset($data->addressCountry)) {
				$this->address->setAddressCountry($data->addressCountry);
			}
		}

		if (isset($data->jobTitle)) {
			$this->setJobTitle($data->jobTitle);
		}

		if (isset($data->worksFor)) {
			$this->setWorksFor(new Organization());
			$this->worksFor->setName($data->worksFor);

			if (isset($data->orgAddr)) {
				$this->worksFor->setAddress(new PostalAddress($data->orgAddr));
			}

			if (isset($data->orgPhone)) {
				$this->worksFor->setTelephone($data->orgPhone);
			}

			if (isset($data->orgEmail)) {
				$this->worksFor->setEmail($data->orgEmail);
			}

			if (isset($data->orgUrl)) {
				$this->worksFor->setUrl($data->orgUrl);
			}

			if (isset($data->orgImg)) {
				$this->worksFor->setImage(new ImageObject($data->orgImg));
			}

			if (isset($data->orgLogo)) {
				$this->worksFor->setLogo(new ImageObject($data->orgLogo));
			}
		}
	}

	public function setGivenName(string $first)
	{
		$this->_set('givenName', $first);
	}

	public function setAdditionalName(string $middle)
	{
		$this->_set('additionalName', $middle);
	}

	public function setFamilyName(string $last)
	{
		$this->_set('familyName', $last);
	}

	public function setEmail(string $email)
	{
		if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->_set('email', $email);
		} else {
			throw new \InvalidArgumentException(sprintf('"%s" is not a valid email address', $email));
		}
	}

	public function setWorksFor(Organization $org)
	{
		$this->_set('worksFor', $org);
	}

	public function setJobTitle(string $title)
	{
		$this->_set('jobTitle', $title);
	}

	public function setTelephone(string $phone)
	{
		$this->_set('telephone', $phone);
	}

	public function setAddress(PostalAddress $addr)
	{
		$this->_set('address', $addr);
	}

	public function setBirthDate(DateTime $bday)
	{
		$this->_set('birthDate', $bday->format('Y-m-d'));
	}
}
