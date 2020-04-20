<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{
	Singleton,
	SAPILoggerTrait,
	LoggerInterpolatorTrait,
	SPLObserverLoggerTrait,
	ExceptionLoggerTrait,
};

use \SPLObserver;

use \shgysk8zer0\PHPAPI\Abstracts\{AbstractLogger};

class SAPILogger extends AbstractLogger implements SPLObserver
{
	use LoggerInterpolatorTrait;
	use SAPILoggerTrait;
	use Singleton;
	use ExceptionLoggerTrait;
	use SPLObserverLoggerTrait;
}
