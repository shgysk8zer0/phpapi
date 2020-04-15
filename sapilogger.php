<?php
namespace shgysk8zer0\PHPAPI;

class SAPILogger extends Abstracts\AbstractLogger
{
	use Traits\LoggerInterpolatorTrait;

	final public function log(string $level, string $message, array $context = []): void
	{
		$msg = sprintf('[%s] "%s"', $level, $this->interpolate($message, $context));
		error_log($msg, 4);
	}
}
