<?php
namespace shgysk8zer0\PHPAPI\Schema;

use \shgysk8zer0\PHPAPI\Interfaces\{InputData};
use \shgysk8zer0\PHPAPI\Schema\{PostalAddress};
use \DateTime;

class Person extends Thing
{
	const TYPE = 'Person';

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

	public function save(): int
	{
		$stm = static::$_pdo->prepare('INSERT INTO `Person` (
			`identifier`,
			`givenName`,
			`additionalName`,
			`familyName`,
			`address`,
			`email`,
			`telephone`,
			`birthDate`,
			`gender`,
			`jobTitle`,
			`worksFor`,
			`honorificPrefix`,
			`honorificSuffix`,
			`image`
		) VALUES (
			:identifier,
			:givenName,
			:additionalName,
			:familyName,
			:address,
			:email,
			:telephone,
			:birthDate,
			:gender,
			:jobTitle,
			:worksFor,
			:honorificPrefix,
			:honorificSuffix,
			:image
		);');

		if ($stm->execute([
			':identifier'      => new UUDI(),
			':givenName'       => $this->givenName,
			':additionalName'  => $this->additionalName ?? null,
			':familyName'      => $this->familyName,
			':address'         => isset($this->address) ? $this->address->getId() : null,
			':email'           => $this->email ?? null,
			':telephone'       => $this->telephone ?? null,
			':birthDate'       => $this->birthDate ?? null,
			':gender'          => $this->gender ?? null,
			':jobTitle'        => $this->jobTitle ?? null,
			':worksFor'        => isset($this->worksFor) ? $this->worksFor->getId() : null,
			':honorificPrefix' => $this->honorificPrefix ?? null,
			':honorificSuffix' => $this->honorificSuffix ?? null,
			':image'           => isset($this->image) ? $this->image->getId() : null,
		])) {
			return static::$_pdo->lastInsertId();
		} else {
			return 0;
		}
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

		if (isset($data->image)) {
			$data->image = new ImageObject($data->image);
		}

		$this->_setDataObject($data);
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
