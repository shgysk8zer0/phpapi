<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Traits\{
	LoggerInterpolatorTrait,
	ExceptionLoggerTrait,
	SAPILoggerTrait,
	Singleton,
};

class SAPILogger extends Abstracts\AbstractLogger
{
	use LoggerInterpolatorTrait;
	use SAPILogTrait;
	use Singleton;
	use ExceptionLoggerTrait;
}
