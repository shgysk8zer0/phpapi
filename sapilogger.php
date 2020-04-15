<?php
namespace shgysk8zer0\PHPAPI;

use \InvalidArgumentException;

class SAPILogger extends Abstracts\AbstractLogger
{
	public const SAPI_CLIENTS = ['cli', 'cli-server'];

	use Traits\LoggerInterpolatorTrait;

	final public function log(string $level, string $message, array $context = []): void
	{
		if (! in_array($level, Abstracts\LogLevel::ALL_LEVELS)) {
			throw new InvalidArgumentException(sprintf('Invalid log level: "%s"', $level));
		} elseif (in_array(PHP_SAPI, self::SAPI_CLIENTS)) {
			$msg = sprintf('[%s] "%s"', $level, $this->interpolate($message, $context));
			error_log($msg, 4);
		}
	}
}
