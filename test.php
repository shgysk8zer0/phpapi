<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Abstracts\LogLevel;
spl_autoload_register('spl_autoload');
set_include_path(dirname(__DIR__, 2));
date_default_timezone_set('America/Los_Angeles');

final class Test implements Interfaces\LoggerAwareInterface
{
	use Traits\LoggerAwareTrait;

	public function error(string $message, array $context = []): void
	{
		if ($this->hasLogger()) {
			$this->logger->info($message, $context);
		}
	}
}

$test = new Test();
$test->setLogger(new SAPILogger());

if (!$test->hasLogger()) {
	exit('Failed setting logger');
}
$test->error('Hello World!');
