<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};
use \InvalidArgumentException;

trait SAPILogTrait
{
	final public function log(string $level, string $message, array $context = []): void
	{
		if (! in_array($level, LogLevel::ALL_LEVELS)) {
			throw new \InvalidArgumentException(sprintf('Invalid log level: "%s"', $level));
		} elseif (in_array(PHP_SAPI, ['cli', 'cli-server'])) {
			$msg = sprintf('[%s] "%s"', $level, $this->interpolate($message, $context));
			error_log($msg, 4);
		}
	}

	abstract public function interpolate(string $message, array $context = []): string;
}
