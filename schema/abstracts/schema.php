<?php
namespace shgysk8zer0\PHPAPI\Schema\Abstracts;

use \JSONSerializable;
use \shgysk8zer0\PHPAPI\{PDO, URL, Headers};
use \shgysk8zer0\PHPAPI\Interfaces\{InputData};
use \shgysk8zer0\PHPAPI\Schema\Interfaces\{Schema as SchemaInterface};
use \shgysk8zer0\PHPAPI\Schema\Traits\{Schema as SchemaTrait};

abstract class Schema implements JSONSerializable, SchemaInterface
{
	use SchemaTrait;

	const CONTEXT = 'https://schema.org/';

	const TYPE    = 'Thing';

	const CONTENT_TYPE = 'application/ld+json';

	public function __construct(int $id = null)
	{
		if (isset($id)) {
			if ($data = $this->_init($id)) {
				$this->_setData($data);
			}
		}
	}

	final public function __isset(string $prop): bool
	{
		return array_key_exists($prop, $this->_data);
	}

	final public function __get(string $prop)
	{
		if (function_exists([$this, 'get' . ucfirst($prop)])) {
			call_user_func([$this, 'get' . ucfirst($prop)]);
		} else {
			return $this->_data[$prop] ?? null;
		}
	}

	final public function __set(string $prop, $value)
	{
		if (function_exists([$this, 'set' . ucfirst($prop)])) {
			call_user_func([$this, 'set' . ucfirst($prop)], $value);
		}
	}

	final public function getScript(): string
	{
		return sprintf('<script type="%s">%s</script>', self::CONTENT_TYPE, json_encode($this));
	}

	final public function __toString(): string
	{
		return $this->getScript();
	}

	final public static function getSchemaURL(): string
	{
		return new URL(static::TYPE, static::CONTEXT);
	}

	final public static function openSchemaDocs()
	{
		Headers::redirect(static::getSchemaURL());
	}

	final protected function _init(int $id): \StdClass
	{
		if (isset(static::$_pdo)) {
			$sql     = sprintf('SELECT * FROM `%s` WHERE `id` = :id LIMIT 1;', $this::TYPE);
			$stm     = static::$_pdo->prepare($sql);
			$stm->id = $id;
			$stm->execute();
			return $stm->fetchObject();
		} else {
			return new \StdClass();
		}
	}

	abstract protected function _setData(\StdClass $data);
}
