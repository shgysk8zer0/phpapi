<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{
	LoggerAwareInterface,
	LoggerAwareLoggerInterface,
};
use \shgysk8zer0\PHPAPI\Traits\{
	LoggerAwareTrait,
	LoggerAwareLoggerTrait,
	Singleton,
	ExceptionLoggerTrait,
};
use \Throwable;

class ExceptionLogger implements LoggerAwareInterface, LoggerAwareLoggerInterface
{
	use Singleton;
	use LoggerAwareTrait;
	use LoggerAwareLoggerTrait;
	use ExceptionLoggerTrait;

	final protected function __construct()
	{
		$this->setLogger(new NullLogger());
	}
}
