<?php
namespace shgysk8zer0\PHPAPI\Schema\Interfaces;
use \shgysk8zer0\PHPAPI\{PDO};
use \shgysk8zer0\PHPAPI\Interfaces\{InputData};
use \shgysk8zer0\PHPAPI\Schema\{Thing};

interface Schema
{
	public static function create(InputData $input): Thing;

	public function delete(): bool;

	public static function setPDO(PDO $pdo);
}
