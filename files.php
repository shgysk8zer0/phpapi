<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\{HTTPException, File};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \shgysk8zer0\PHPAPI\Traits\{Singleton};
use \JSONSerializable;

final class Files implements JSONSerializable
{
	use Singleton;
	private $_files = [];
	private $_path = '';

	final private function __construct()
	{
		foreach($_FILES as $key => $file) {
			$this->_files[$key] = new File($key);
		}
	}

	final public function __get(string $key)
	{
		if (isset($this->{$key})) {
			return $this->_files[$key];
		}
	}

	final public function __isset(string $key): bool
	{
		return array_key_exists($key, $this->_files);
	}

	final public function __debugInfo(): array
	{
		return $this->_files;
	}

	final public function jsonSerialize(): array
	{
		return $this->_files;
	}
}
