<?php

namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\Traits\{PDOParamTypes};

class PDO extends \PDO
{
	use PDOParamTypes;

	const OPTIONS = [
		self::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'',
		self::ATTR_ERRMODE            => self::ERRMODE_EXCEPTION,
		self::ATTR_DEFAULT_FETCH_MODE => self::FETCH_OBJ,
		self::ATTR_STATEMENT_CLASS    => [__NAMESPACE__ . '\\PDOStatement'],
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
