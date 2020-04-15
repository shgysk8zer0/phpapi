<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Interfaces\{LoggerInterface};

/**
 * Basic Implementation of LoggerAwareInterface.
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger = null;

    /**
     * Sets a logger.
     *
     * @param LoggerInterface $logger
     */
    final public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

		/**
		 * Gets the set logger instance for the object
		 *
		 * @param void
		 * @return LoggerInterface The previously set logger or null
		 */
		final public function getLogger():? LoggerInterface
		{
			return $this->logger;
		}

		/**
		 * Check if a logger is set
		 * @return bool If a logger is set
		 */
		public function hasLogger(): bool
		{
			return isset($this->logger);
		}
}
