<?php
namespace shgysk8zer0\PHPAPI\Schema\Interfaces;
use \shgysk8zer0\PHPAPI\{PDO};

interface Schema
{
	public function create(): bool;

	public function delete(): bool;

	public static function setPDO(PDO $pdo);
}
