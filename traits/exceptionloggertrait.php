<?php
namespace shgysk8zer0\PHPAPI\Traits;
use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};
use \Throwable;

trait ExceptionLoggerTrait
{
	final public function registerErrorHandler(): void
	{
		set_error_handler([$this, 'logError']);
	}

	final public function registerExceptionHandler(): void
	{
		set_exception_handler([$this, 'logException']);
	}

	final public function logException(Throwable $e, string $level = LogLevel::ERROR): void
	{
		$this->log($level, '[{class} {code}] "{message}" at {file}:{line}', [
			'class'   => get_class($e),
			'code'    => $e->getcode(),
			'message' => $e->getMessage(),
			'file'    => $e->getFile(),
			'line'    => $e->getLine(),
		]);
	}

	final public function logError(
		int    $errno,
		string $errstr,
		string $errfile    = 'unknown file',
		int    $errline    = 0,
		array  $errcontext = []
	): bool
	{
		$level = 'warn';
		$message ='"{errstr}" at {errfile}:{errline}';
		$context = ['errstr' => $errstr, 'errfile' => $errfile, 'errline' => $errline];

		switch($errno) {
			case E_PARSE:
			case E_CORE_ERROR:
			case E_COMPILE_ERROR:
				$this->critical($message, $context);
				break;

			case E_ERROR:
			case E_USER_ERROR:
				$this->error($message, $context);
				break;

			case E_WARNING:
			case E_USER_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
				$this->warning($message, $context);
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
			case E_RECOVERABLE_ERROR:
				$this->notice($message, $context);
				break;

			case E_DEPRECATED:
			case E_USER_DEPRECATED:
			case E_STRICT:
				$this->debug($message, $context);
				break;
		}

		return true;
	}

	abstract public function critical(string $message, array $context = []): void;

	abstract public function error(string $message, array $context = []): void;

	abstract public function warning(string $message, array $context = []): void;

	abstract public function notice(string $message, array $context = []): void;

	abstract public function debug(string $message, array $context = []): void;

	abstract public function log(string $level, string $message, array $context = []): void;
}
