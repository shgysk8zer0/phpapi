<?php

namespace shgysk8zer0\PHPAPI;
use \PDO;
use \shgysk8zer0\PHPAPI\Traits\{PDOParamTypes, LoggerAwareTrait};
use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface};

final class PDOStatement extends \PDOStatement
{
	use PDOParamTypes;
	use LoggerAwareTrait;

	final public function __toString(): string
	{
		return $this->queryString;
	}

	final public function __set(string $param, $value): void
	{
		$this->bindValue(":{$param}", $value, static::getParamType($value));
	}

	final public function __invoke(array $input_params = null): \Generator
	{
		if (isset($input_params)) {
			$this->execute($input_params);
		} else {
			$this->execute();
		}

		while($result = $this->fetch()) {
			yield $result;
		}
	}
}
