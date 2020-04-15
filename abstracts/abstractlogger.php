<?php
namespace shgysk8zer0\PHPAPI\Abstracts;
use \shgysk8zer0\PHPAPI\Interfaces\{LoggerInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerTrait};

abstract class AbstractLogger implements LoggerInterface
{
	use LoggerTrait;

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed   $level
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 *
	 * @throws \Psr\Log\InvalidArgumentException
	 */
	abstract public function log(string $level, string $message, array $context = []): void;
}
