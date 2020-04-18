<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface, LoggerInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerAwareTrait, Singleton};
use \Throwable;

class ExceptionLogger implements LoggerAwareInterface
{
	use Singleton;
	use LoggerAwareTrait;

	final protected function __construct()
	{
		$this->setLogger(new NullLogger());
		// noop
		// Prevent creating other instances by only allowing access via `::getInstace()`
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
				$this->logger->critical($message, $context);
				break;

			case E_ERROR:
			case E_USER_ERROR:
				$this->logger->error($message, $context);
				break;

			case E_WARNING:
			case E_USER_WARNING:
			case E_CORE_WARNING:
			case E_COMPILE_WARNING:
				$this->logger->warning($message, $context);
				break;

			case E_NOTICE:
			case E_USER_NOTICE:
			case E_RECOVERABLE_ERROR:
				$this->logger->notice($message, $context);
				break;

			case E_DEPRECATED:
			case E_USER_DEPRECATED:
			case E_STRICT:
				$this->logger->debug($message, $context);
				break;
		}

		return true;
	}

	final public function logException(Throwable $e): void
	{
		$this->logger->error('[{class}] "{message}" at {file}:{line}', [
			'message' => $e->getMessage(),
			'file'    => $e->getFile(),
			'line'    => $e->getLine(),
			'class'   => get_class($e),
		]);
	}

	final public static function registerErrorHandler(): void
	{
		set_error_handler([static::getInstance(), 'logError']);
	}

	final static public function registerExceptionHandler(): void
	{
		set_exception_handler([static::getInstance(), 'logException']);
	}
}
