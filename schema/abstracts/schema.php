<?php
namespace shgysk8zer0\PHPAPI\Schema\Abstracts;

use \JSONSerializable;
use \shgysk8zer0\PHPAPI\{PDO, URL, Headers, UUID};
use \shgysk8zer0\PHPAPI\Interfaces\{InputData};
use \shgysk8zer0\PHPAPI\Schema\Interfaces\{Schema as SchemaInterface};
use \shgysk8zer0\PHPAPI\Schema\Traits\{Schema as SchemaTrait};

abstract class Schema implements JSONSerializable, SchemaInterface
{
	use SchemaTrait;

	public const CONTEXT = 'https://schema.org/';

	public const TYPE    = 'Thing';

	public const CONTENT_TYPE = 'application/ld+json';

	public function __construct(int $id = null)
	{
		if (isset($id)) {
			if ($data = $this->_init('id', $id) and isset($data->id)) {
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
		if (method_exists($this, 'get' . ucfirst($prop))) {
			call_user_func([$this, 'get' . ucfirst($prop)]);
		} else {
			return $this->_data[$prop] ?? null;
		}
	}

	final public function __set(string $prop, $value): void
	{
		if (method($this, 'set' . ucfirst($prop))) {
			call_user_func([$this, 'set' . ucfirst($prop)], $value);
		}
	}

	final public function __toString(): string
	{
		return $this->getScript();
	}

	final public function getByUuid(string $uuid): bool
	{
		if ($data = $this->_init('identifier', $uuid) and isset($data->id)) {
			$this->_setData($data);
			return true;
		} else {
			return false;
		}
	}

	final public function getScript(): string
	{
		return sprintf(
			'<script type="%s">%s</script>',
			self::CONTENT_TYPE,
			json_encode($this)
		);
	}

	final public static function getSchemaURL(): string
	{
		return new URL(static::TYPE, static::CONTEXT);
	}

	final public static function openSchemaDocs()
	{
		Headers::redirect(static::getSchemaURL());
	}

	final protected function _init(string $key, $val): ?object
	{
		if (isset(static::$_pdo)) {
			$sql     = sprintf('SELECT * FROM `%s` WHERE `%s` = :val LIMIT 1;', $this::TYPE, $key);
			$stm     = static::$_pdo->prepare($sql);
			$stm->execute([':val' => $val]);
			return $stm->fetchObject();
		} else {
			return null;
		}
	}

	abstract protected function _setData(object $data);
}
