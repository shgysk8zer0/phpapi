<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

/**
 * Allows public access to class's logger, though class itself does not implement
 * LoggerInterface.
 */
interface LoggerAwareLoggerInterface extends LoggerAwareInterface
{
	/**
	 * System is unusable.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function emergency(string $message, array $context = []): void;

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function alert(string $message, array $context = []): void;

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function critical(string $message, array $context = []): void;

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function error(string $message, array $context = []): void;

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function warning(string $message, array $context = []): void;

	/**
	 * Normal but significant events.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function notice(string $message, array $context = []): void;

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function info(string $message, array $context = []): void;

	/**
	 * Detailed debug information.
	 *
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function debug(string $message, array $context = []): void;

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed   $level
	 * @param string  $message
	 * @param mixed[] $context
	 *
	 * @return void
	 */
	public function log(string $level, string $message, array $context = []): void;
}
