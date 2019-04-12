<?php
namespace shgysk8zer0\PHPAPI\Schema\Traits;
use \shgysk8zer0\PHPAPI\{PDO};

trait Schema
{
	protected static $_pdo = null;

	protected $_data = [];

	final public static function setPDO(PDO $pdo)
	{
		static::$_pdo = $pdo;
	}

	final protected function _get(string $key)
	{
		return $this->_data[$key] ?? null;
	}

	final protected function _set(string $prop, $value)
	{
		$this->_data[$prop] = $value;
	}

	final protected function _setDataObject(\StdClass $data)
	{
		$vars = get_object_vars($data);
		foreach ($vars as $key => $value) {
			if (is_null($value)) {
				unset($vars[$key]);
			}
		}
		$this->_data = $vars;
	}

	public function jsonSerialize(): array
	{
		$data = $this->_data;
		unset($data['created'], $data['updated'], $data['id']);
		return array_merge(['@context' => $this::CONTEXT, '@type' => $this::TYPE], $data);
	}
}
