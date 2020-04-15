<?php

namespace shgysk8zer0\PHPAPI\Interfaces;
use \shgysk8zer0\PHPAPI\Interfaces\{LoggerInterface};
/**
 * Describes a logger-aware instance.
 */
interface LoggerAwareInterface
{
    /**
     * Sets a logger instance on the object.
     *
     * @param LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(LoggerInterface $logger);

		/**
		 * Gets the set logger instance for the object
		 *
		 * @param void
		 * @return LoggerInterface The previously set logger or null
		 */
		public function getLogger():? LoggerInterface;

		/**
		 * Check if a logger is set
		 * @return bool If a logger is set
		 */
		public function hasLogger(): bool;
}
