<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{
	LoggerInterpolatorTrait,
	ExceptionLoggerTrait,
	SystemLoggerTrait,
	Singleton,
};

class SystemLogger extends Abstracts\AbstractLogger
{
	use LoggerInterpolatorTrait;
	use SystemLoggerTrait;
	use Singleton;
	use ExceptionLoggerTrait;
}
