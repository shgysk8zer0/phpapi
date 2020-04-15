<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \PDO;
use \PDOStatement;

trait PDOAccessTrait
{
	private static $_pdo = null;

	public static function setPDO(?PDO $pdo): void
	{
		static::$_pdo = $pdo;
	}

	protected static function _getPDO():? PDO
	{
		return static::$_pdo;
	}

	protected static function _prepare(string $sql):? PDOStatement
	{
		if (static::$_pdo !== null) {
			return static::$_pdo->prepare($sql);
		} else {
			return null;
		}
	}
}
