<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{
	ExceptionLoggerTrait,
	LoggerInterpolatorTrait,
	LoggerLevelsTrait,
	PDOAwareTrait,
	SPLObserverLoggerTrait,
};

use \shgysk8zer0\PHPAPI\Abstracts\{AbstractLogger};

use \PDO;

use \SPLObserver;

use \InvalidArgumentException;

class PDOLogger extends AbstractLogger implements SPLObserver
{
	use ExceptionLoggerTrait;
	use LoggerInterpolatorTrait;
	use LoggerLevelsTrait;
	use PDOAwareTrait;
	use SPLObserverLoggerTrait;

	private $_msg_length = 255;

	private $_table = 'logger';

	private $_cols = [
		'uuid'     => 'uuid',
		'level'    => 'level',
		'message'  => 'message',
		'datetime' => 'datetime',
	];

	final public function __construct(PDO $pdo = null)
	{
		if (isset($pdo)) {
			$this->setPDO($pdo);
		}
	}

	final public function getColumn(string $name): string
	{
		if (array_key_exists($name, $this->_cols)) {
			return $this->_cols[$name];
		} else {
			throw new InvalidArgumentException(sprintf('Invalid column "%s"', $name));
		}
	}

	final public function getColumns(string ...$cols): array
	{
		return array_map(function(string $col): string
		{
			return $this->getColumn($col);
		}, $cols);
	}

	final public function setColumn(string $name, string $value): bool
	{
		if (array_key_exists($name, $this->_data)) {
			$this->_data[$name] = $value;
			return true;
		} else {
			return false;
		}
	}

	final public function setColumns(array $cols = []): bool
	{
		$ret = true;

		foreach ($cols as $key => $value) {
			if (! $this->setColumn($key, $value)) {
				$ret = false;
				break;
			}
		}

		return $ret;
	}

	final public function getMessageLength(): int
	{
		return $this->_msg_length;
	}

	final public function setMessageLength(int $len): void
	{
		$this->_msg_length = $len;
	}

	final public function getTable(): string
	{
		return $this->_table;
	}

	final public function setTable(string $table): void
	{
		$this->_table = $table;
	}

	/**
     * Logs with an arbitrary level.
     *
     * @param mixed   $level
     * @param string  $message
     * @param mixed[] $context
     *
     * @return void
     */
	public function log(string $level, string $message, array $context = []): void
	{
		if (! $this->validLevel($level)) {
			throw new InvalidArgumentException(sprintf('Invalid log level: "%s"', $level));
		} elseif ($this->allowsLevel($level)) {
			if ($stm = $this->_prepare("INSERT INTO `{$this->getTable()}` (
				`{$this->getColumn('uuid')}`,
				`{$this->getColumn('level')}`,
				`{$this->getColumn('message')}`,
				`{$this->getColumn('datetime')}`
			) VALUES (
				:uuid,
				:level,
				:message,
				:datetime
			);", 'log')) {
				$stm->execute([
					'uuid'     => new UUID(),
					'level'    => $level,
					'message'  => substr($this->interpolate($message, $context), 0, $this->getMessageLength()),
					'datetime' => date('Y-m-d H:i:s'),
				]);
			}
		}
	}
}
