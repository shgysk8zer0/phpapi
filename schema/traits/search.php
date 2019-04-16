<?php
namespace shgysk8zer0\PHPAPI\Schema\Traits;

trait Search
{
	final protected static function _simpleSearch(string $col, string $value, int $limit = 10, int $offset = 0): array
	{
		$sql = sprintf(
			'SELECT `id` FROM `%s` WHERE `%s` LIKE :value LIMIT %d OFFSET %d;',
			static::TYPE,
			$col,
			$limit,
			$offset
		);

		$stm = static::$_pdo->prepare($sql);
		$stm->execute([':value' => $value]);
		$results = $stm->fetchAll();

		return array_map(function(\StdClass $result): self
		{
			return new self($result->id);
		}, $results);
	}

	final protected static function _search(string $table, array $where, int $limit = 10, int $offset = 0): array
	{
		$keys = array_map(function(string $key): string
		{
			return ":{$key}";
		}, array_keys($where));
		$params = array_map(function(string $key): string
		{
			$k = str_replace('`', '``', $key);
			return "`{$key}` LIKE :{$key}";
		}, array_keys($where));

		$sql = sprintf(
			'SELECT * FROM `%s` WHERE %s LIMIT %d OFFSET %d;',
			static::TYPE,
			join(' AND ', $params),
			$limit,
			$offset
		);
		$stm = static::$_pdo->prepare($sql);
		$stm->execute(array_combine($keys, array_values($where)));
		return $stm->fetchAll();
	}

	final public static function searchByName(string $name, int $limit = 10, int $offset = 0): array
	{
		return static::_simpleSearch('name', "%{$name}%", $limit, $offset);
	}

	final public static function searchByUrl(string $url, int $limit = 10, int $offset = 0): array
	{
		return static::_simpleSearch('url', "%{$url}%", $limit, $offset);
	}
}
