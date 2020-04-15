<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Abstracts\{AbstractLogger, LogLevel};
use \InvalidArgumentException;

/**
 * This Logger can be used to avoid conditional log calls.
 *
 * Logging should always be optional, and if no logger is provided to your
 * library creating a NullLogger instance to have something to throw logs at
 * is a good way to avoid littering your code with `if ($this->logger) { }`
 * blocks.
 */
final class NullLogger extends AbstractLogger
{
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    final public function log(string $level, string $message, array $context = []): void
    {
        if (! in_array($level, Abstracts\LogLevel::ALL_LEVELS)) {
					throw new InvalidArgumentException(sprintf('Invalid log level: "%s"', $level));
				}
    }
}
