<?php

namespace shgysk8zer0\PHPAPI\Traits;

use \InvalidArgumentException;

trait PDOCacheTrait
{
	private $_table = 'cache';

	private $_cols  = [
		'key'     => 'key',
		'value'   => 'value',
		'expires' => 'expires',
		'created' => 'created',
	];

	final public function getColumn(string $name): string
	{
		if (array_key_exists($name, $this->_cols)) {
			return $this->_cols[$name];
		} else {
			throw new InvalidArgumentException(sprintf('Invalid column "%s"', $name));
		}
	}

	final public function getColumns(string ...$cols): array
	{
		return array_map(function(string $col): string
		{
			return $this->getColumn($col);
		}, $cols);
	}

	final public function setColumn(string $name, string $value): bool
	{
		if (array_key_exists($name, $this->_data)) {
			$this->_data[$name] = $value;
			return true;
		} else {
			return false;
		}
	}

	final public function setColumns(array $cols = []): bool
	{
		$ret = true;

		foreach ($cols as $key => $value) {
			if (! $this->setColumn($key, $value)) {
				$ret = false;
				break;
			}
		}

		return $ret;
	}

	final public function getTable(): string
	{
		return $this->_table;
	}

	final public function setTable(string $table): void
	{
		$this->_table = $table;
	}
}
