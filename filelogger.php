<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{
	LoggerInterpolatorTrait,
	SPLObserverLoggerTrait,
};

use shgysk8zer0\PHPAPI\Abstracts\{AbstractLogger, LogLevel};

use \SPLObserver;
use \InvalidArgumentException;

class FileLogger extends AbstractLogger implements SPLObserver
{
	use LoggerInterpolatorTrait;
	use SPLObserverLoggerTrait;

	public const LOG_FILE = 'errors.log';

	private $_file = null;

	public function __construct(string $file = self::LOG_FILE)
	{
		$this->_file = $file;
	}

	final public function log(string $level, string $message, array $context = []): void
	{
		if (! in_array($level, LogLevel::ALL_LEVELS)) {
			throw new InvalidArgumentException(sprintf('Invalid log level: "%s"', $level));
		} else {
			$msg = sprintf('[%s] %s', $level, $this->interpolate($message, $context));
			error_log($msg . PHP_EOL, 3, $this->_file);
		}
	}
}
