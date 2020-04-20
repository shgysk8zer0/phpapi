<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{
	LoggerInterpolatorTrait,
	ExceptionLoggerTrait,
	SystemLoggerTrait,
	Singleton,
	SPLObserverLoggerTrait,
};

use \shgysk8zer0\PHPAPI\Abstracts\{AbstractLogger};
use \SPLObserver;

class SystemLogger extends AbstractLogger implements SPLObserver
{
	use LoggerInterpolatorTrait;
	use SystemLoggerTrait;
	use Singleton;
	use ExceptionLoggerTrait;
	use SPLObserverLoggerTrait;
}
