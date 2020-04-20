<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};
use \InvalidArgumentException;

trait SystemLoggerTrait
{
	final public function log(string $level, string $message, array $context = []): void
	{
		if (! in_array($level, LogLevel::ALL_LEVELS)) {
			throw new InvalidArgumentException(sprintf('Invalid log level: "%s"', $level));
		} else {
			switch ($level) {
				case LogLevel::EMERGENCY:
					$lvl = LOG_EMERG;
					break;

				case LogLevel::ALERT:
					$lvl = LOG_ALERT;
					break;

				case LogLevel::CRITICAL:
					$lvl = LOG_CRIT;
					break;

				case LogLevel::ERROR:
					$lvl = LOG_ERR;
					break;

				case LogLevel::WARNING;
					$lvl = LOG_WARNING;
					break;

				case LogLevel::NOTICE:
					$lvl = LOG_NOTICE;
					break;

				case LogLevel::INFO;
					$lvl = LOG_INFO;
					break;

				case LogLevel::DEBUG;
					$lvl = LOG_DEBUG;
					break;

				case LogLevel::INFO:
					$lvl = LOG_INFO;
					break;

				default:
					throw new InvalidArgumentExcetpion(sprintf('Invalid log level: %s', $level));
			}

			openlog(__CLASS__, LOG_ODELAY, LOG_DAEMON);
			$ret = syslog($lvl, $this->interpolate($message, $context));
			closelog();
		}
	}

	abstract public function interpolate(string $message, array $context = []): string;
}
