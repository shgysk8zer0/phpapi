<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{
	SPLSubjectTrait,
	SPLSubjectNotifyTrait,
	SplSubjectLoggerTrait,
	ExceptionLoggerTrait,
	LoggerTrait,
	Singleton,
};

use \shgysk8zer0\PHPAPI\Interfaces\{
	SPLSubjectLoggerInterface,
	LoggerInterface,
};

use \SPLSubject;

class LoggerSubject implements SPLSubjectLoggerInterface, LoggerInterface
{
	use SPLSubjectTrait;
	use SPLSubjectNotifyTrait;
	use SplSubjectLoggerTrait;
	use ExceptionLoggerTrait;
	use LoggerTrait;
	use Singleton;

	final public function __debugInfo(): array
	{
		return [
			'level'   => $this->getLevel(),
			'message' => $this->getMessage(),
			'context' => $this->getContext(),
		];
	}

	final public function log(string $level, string $message, array $context = []): void
	{
		$this->setLevel($level);
		$this->setMessage($message);
		$this->setContext($context);
		$this->notify();
	}
}
