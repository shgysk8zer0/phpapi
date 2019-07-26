<?php
namespace shgysk8zer0\PHPAPI\Schema\Traits;
use \shgysk8zer0\PHPAPI\{PDO};
use \shgysk8zer0\PHPAPI\Interfaces\{InputData};
use \shgysk8zer0\PHPAPI\Schema\{Thing};

trait Schema
{
	protected static $_pdo = null;

	protected $_data = [];

	protected $_id = 0;

	protected static $_stms = [];

	public function __debugInfo(): array
	{
		$data = $this->_data;
		$data['id'] = $this->_id;
		return $data;
	}

	final protected function _setId(int $id)
	{
		$this->_id = $id;
	}

	final protected function _getId(): int
	{
		return $this->_id;
	}

	final public static function setPDO(PDO $pdo)
	{
		static::$_pdo = $pdo;
	}

	public static function create(InputData $input): Thing
	{
		return new self();
	}

	public function delete(): bool
	{
		if ($this->_getId() !== 0) {
			$sql = sprintf('DELETE FROM `%s` WHERE `id` = :id LIMIT 1;', $this::TYPE);
			$stm = $this->_pdo->prepare($sql);

			if ($stm->execute([':id' => $this->_getId()])) {
				return $stm->rowCount() === 1;
			} else {
				return false;
			}
		} else {
			return true;
		}
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

	final protected function _getStm(string $key): \PDOStatement
	{
		if (! array_key_exists($this::TYPE, static::$_stms)) {
			static::$_stms[$this::TYPE] = [];
		}

		if (! array_key_exists($key, static::$_stms[$this::TYPE])) {
			$sql = sprintf($this::QUERY, $this::TYPE, $key);
			static::$_stms[$this::TYPE][$key] = static::$_pdo->prepare($sql);
		}

		return static::$_stms[$this::TYPE][$key];
	}
}
