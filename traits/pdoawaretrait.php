<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \PDO;

use \PDOStatement;

trait PDOAwareTrait
{
	private $_pdo = null;

	private $_stms = [];

	final public function setPDO(?PDO $pdo): void
	{
		$this->_pdo = $pdo;
		$this->_stms = [];
	}

	final protected function hasPDO(): bool
	{
		return isset($this->_pdo);
	}

	final protected function _hasStatement(?string $key = null): bool
	{
		return isset($key) and array_key_exists($key, $this->_stms);
	}

	final protected function _getStatement(string $key):? PDOStatement
	{
		if ($this->_hasStatement($key)) {
			return $this->_stms[$key];
		} else {
			return null;
		}
	}

	final protected function _setStatement(string $key, PDOStatement $stm): void
	{
		$this->_stms[$key] = $stm;
	}

	final protected function _prepare(string $sql, ?string $key = null):? PDOStatement
	{
		if (! $this->hasPDO()) {
			return null;
		} elseif ($this->_hasStatement($key)) {
			return $this->_getStatement($key);
		} else {
			$stm = $this->_pdo->prepare($sql);

			if (isset($key)) {
				$this->_setStatement($key, $stm);
			}

			return $stm;
		}
	}

	final protected function _inTransaction(): bool
	{
		return $this->hasPDO() and $this->_pdo->inTransaction();
	}

	final protected function _beginTransaction(): bool
	{
		return $this->hasPDO() and $this->_pdo->beginTransaction();
	}

	final protected function _commit(): bool
	{
		return $this->hasPDO() and $this->_pdo->commit();
	}

	final protected function _rollBack(): bool
	{
		return $this->hasPDO() and $this->_pdo->rollBack();
	}

	final protected function _lastInsertId(?string $name = null):? string
	{
		if ($this->hasPDO()) {
			return $this->_pdo->lastInsertId($name);
		} else {
			return null;
		}
	}

	final protected function _getAttribute(int $id)
	{
		if ($this->hasPDO()) {
			return $this->_pdo->getAttribute($id);
		} else {
			return null;
		}
	}

	final protected function _setAttribute(int $id, $value): bool
	{
		if ($this->hasPDO()) {
			return $this->_pdo->setAttribute($id, $value);
		} else {
			return false;
		}
	}
}
