<?php
namespace shgysk8zer0\PHPAPI\Schema\Abstracts;

use \JSONSerializable;
use \StdClass;
use \shgysk8zer0\PHPAPI\{PDO, PDOStatement, URL, Headers, UUID};
use \shgysk8zer0\PHPAPI\Interfaces\{InputData};
use \shgysk8zer0\PHPAPI\Schema\Interfaces\{Schema as SchemaInterface};
use \shgysk8zer0\PHPAPI\Schema\Traits\{Schema as SchemaTrait};

abstract class Schema implements JSONSerializable, SchemaInterface
{
	use SchemaTrait;

	const CONTEXT = 'https://schema.org/';

	const TYPE    = 'Thing';

	const CONTENT_TYPE = 'application/ld+json';

	const COLS = [];

	const REQUIRED = [];

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

	final private function _getCols(): array
	{
		return static::_mapCols(static::COLS);
	}

	final private function _getParams(): array
	{
		return static::_mapParams(static::COLS);
	}

	final protected function _init(string $key, string $val): StdClass
	{
		if (isset(static::$_pdo)) {
			$sql     = sprintf('SELECT * FROM `%s` WHERE `%s` = :val LIMIT 1;', $this::TYPE, $key);
			$stm     = static::_prepare($sql);
			$stm->execute([':val' => $val]);
			return $stm->fetchObject();
		} else {
			return new StdClass();
		}
	}

	final public function insert(array $values): int
	{
		$keys = array_keys($values);
		$cols = static::_mapCols(...$keys);
		$params = static::_mapParams(...$keys);
		$bind = array_combine($params, array_values($values));

		$sql = sprintf(
			'INSERT INTO `%s` (%s) VALUES (%s)',
			static::TYPE,
			join(', ', $cols),
			join(', ', $params)
		);

		$stm = static::_prepare($sql);

		if ($stm->execute($bind)) {
			return static::_lastInsertId();
		} else {
			return 0;
		}
	}

	final protected static function _prepare(string $query): PDOStatement
	{
		if (isset(static::$_pdo)) {
			return static::$_pdo->prepare($query);
		} else {
			throw new \Exception('No PDO set');
		}
	}

	final protected static function _lastInsertId(): int
	{
		return static::$_pdo->lastInsertId();
	}

	final static protected function _mapParams(string ...$keys): array
	{
		return array_map(function(string $param): string
		{
			return ":{$param}";
		}, $keys);
	}

	final static protected function _mapCols(string ...$keys): array
	{
		return array_map(function(string $col): string
		{
			return "`{$col}`";
		}, $keys);
	}

	abstract protected function _setData(StdClass $data);
}
