<?php
namespace shgysk8zer0\PHPAPI\Schema;

use \shgysk8zer0\PHPAPI\Interfaces\{InputData};

class Thing extends Abstracts\Schema
{
	const TYPE = 'Thing';

	final public function getDescription(): string
	{
		return $this->_get('description');
	}

	final public function setDescription(string $description)
	{
		$this->_set('description', $description);
	}

	final public function getId(): int
	{
		return $this->_getId();
	}

	final public function getName(): string
	{
		return $this->_get('name');
	}

	final public function setName(string $name)
	{
		$this->_set('name', $name);
	}

	public function setImage(ImageObject $img)
	{
		$this->_set('image', $img);
	}

	public function getImage(): ImageObject
	{
		return $this->_get('image');
	}

	public static function create(InputData $input): self
	{
		$thing = new self();
		$this->_setUuid(static::generateUuid());

		if ($input->has('image')) {
			$thing->setImage(new ImageObject($input->get('image')));
		}

		if ($input->has('name')) {
			$thing->setName($input->get('name'));
		}

		if ($input->has('description')) {
			$thing->setDescription($input->get('description'));
		}

		return $thing;
	}

	protected function _setData(\StdClass $data)
	{
		$data->id = intval($data->id);
		$this->_setDataObject($data);
	}
}
