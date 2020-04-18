<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Abstracts\{AbstractLogger, LogLevel};
use \shgysk8zer0\PHPAPI\Traits\{LoggerInterpolatorTrait, Singleton};
use \InvalidArgumentException;

class ConsoleLogger extends AbstractLogger
{
	use LoggerInterpolatorTrait;
	use Singleton;

	public function log(string $level, string $message, array $context = []): void
	{
		$msg = $this->interpolate($message, $context);

		switch($level) {
			case LogLevel::EMERGENCY:
			case LogLevel::ALERT:
			case LogLevel::CRITICAL:
			case LogLevel::ERROR:
				Console::error($msg);
				break;

			case LogLevel::WARNING:
			case LogLevel::NOTICE:
				Console::warn($msg);
				break;

			case LogLevel::INFO:
				Console::info($msg);
				break;

			case LogLevel::DEBUG:
				Console::log($msg);
				break;

			default: throw new InvalidArgumentException("Invalid log level: {$level}");
		}
	}
}
