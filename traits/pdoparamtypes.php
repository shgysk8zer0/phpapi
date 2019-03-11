<?php

namespace shgysk8zer0\PHPAPI\Traits;
use \PDO;

Trait PDOParamTypes
{
	final public static function getParamType($thing): int
	{
		switch(gettype($thing)) {
			case 'string':
			case 'double':
				return PDO::PARAM_STR;
			case 'integer':
				return PDO::PARAM_INT;
			case 'boolean':
				return PDO::PARAM_BOOL;
			case 'NULL':
				return PDO::PARAM_NULL;
			default:
				return PDO::PARAM_STR;
		}
	}
}
