<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};
use \Throwable;

interface ExceptionLoggerInterface extends LoggerInterface
{
	public function logException(Throwable $e, string $level = LogLevel::ERROR): void;

	public function logError(
		int    $errno,
		string $errstr,
		string $errfile    = 'unknown file',
		int    $errline    = 0,
		array  $errcontext = []
	): bool;
}
