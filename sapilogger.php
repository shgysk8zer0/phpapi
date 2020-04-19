<?php
namespace shgysk8zer0\PHPAPI;

class SAPILogger extends Abstracts\AbstractLogger
{
	use Traits\LoggerInterpolatorTrait;
	use Traits\SAPILogTrait;
	use Traits\Singleton;
}
