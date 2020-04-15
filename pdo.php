<?php

namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface};

use \shgysk8zer0\PHPAPI\Traits\{PDOParamTypes, LoggerAwareTrait};

class PDO extends \PDO implements LoggerAwareInterface
{
	use PDOParamTypes;
	use LoggerAwareTrait;

	private const OPTIONS = [
		self::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
		self::ATTR_ERRMODE            => self::ERRMODE_EXCEPTION,
		self::ATTR_DEFAULT_FETCH_MODE => self::FETCH_OBJ,
		self::ATTR_STATEMENT_CLASS    => [__NAMESPACE__ . '\\PDOStatement'],
		self::ATTR_ORACLE_NULLS       => self::NULL_EMPTY_STRING,
	];

	private static $_instances = [];

	private static $_creds_file = null;

	final public function __construct(
		string $username,
		string $password,
		string $database = null,
		string $host     = 'localhost',
		string $charset  = 'UTF8',
		int    $port     = 3306
	)
	{
		$this->setLogger(new NullLogger());

		if (is_null($database)) {
			$database = $username;
		}
		$dsn = new DSN();
		$dsn->setHost($host);
		$dsn->setCharset($charset);
		$dsn->setPort($port);

		if (isset($database)) {
			$dsn->setDatabase($database);
		}

		parent::__construct($dsn, $username, $password, self::OPTIONS);
	}

	public function __invoke(string ...$queries): \Generator
	{
		$this->beginTransaction();

		try {
			foreach ($queries as $query) {
				yield $this->exec($query);
			}
			$this->commit();
		} catch (\Throwable $e) {
			$this->rollBack();
		}
	}

	final public function insert(string $table, array $values): ?int
	{
		$keys = array_map(function(string $key): string
		{
			return "`{$key}`";
		}, array_keys($values));

		$vals = array_map(function(string $key): string
		{
			return ":{$key}";
		}, array_keys($values));

		$sql = sprintf(
			'INSERT INTO `%s` (%s) VALUES (%s);',
			$table,
			join(', ', $keys),
			join(', ', $vals)
		);

		$stm = $this->prepare($sql);
		$stm->setLogger($this->logger);

		if ($stm->execute(array_combine($vals, array_values($values)))) {
			return $this->lastInsertId();
		} else {
			return null;
		}
	}

	final public static function setCredsFile(string $creds_file): void
	{
		static::$_creds_file = $creds_file;
	}

	public static function load(string $creds_file = null): self
	{
		if (is_null($creds_file)) {
			$creds_file = static::$_creds_file;
		}

		if (! array_key_exists($creds_file, static::$_instances)) {
			$data = json_decode(file_get_contents($creds_file));
			static::$_instances[$creds_file] = new self(
				$data->username,
				$data->password,
				$data->database ?? $data->username,
				$data->host ?? 'localhost',
				$data->charset ?? 'UTF8',
				$data->port ?? 3306
			);
		}

		return static::$_instances[$creds_file];
	}
}
