<?php
namespace shgysk8zer0\PHPAPI\Schema;

class Thing extends Abstracts\Schema
{
	const TYPE = 'Thing';

	final public function getDescription(): string
	{
		return $this->_get('description');
	}

	final public function setDescription(string $description): string
	{
		$this->_set('description', $description);
	}

	final public function getId(): int
	{
		return $this->_get('id');
	}

	final public function getName(): string
	{
		return $this->_get('name');
	}

	final public function setName(string $name)
	{
		$this->_set('name', $name);
	}

	public function create(): bool
	{
		return true;
	}

	public function delete(): bool
	{
		return true;
	}

	protected function _setData(\StdClass $data)
	{
		$data->id = intval($data->id);
		$this->_setDataObject($data);
	}
}
