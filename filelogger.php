<?php
namespace shgysk8zer0\PHPAPI;

use \InvalidArgumentException;

class FileLogger extends Abstracts\AbstractLogger
{
	use Traits\LoggerInterpolatorTrait;

	public const LOG_FILE = 'errors.log';

	private $_file = null;

	public function __construct(string $file = self::LOG_FILE)
	{
		$this->_file = $file;
	}

	final public function log(string $level, string $message, array $context = []): void
	{
		if (! in_array($level, Abstracs\LogLevel::ALL_LEVELS)) {
			throw new InvalidArgumentException(sprintf('Invalid log level: "%s"', $level));
		} else {
			$msg = sprintf('[%s] "%s"', $level, $this->interpolate($message, $context));
			error_log($msg . PHP_EOL, 3, $this->_file);
		}
	}
}
