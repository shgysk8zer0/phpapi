<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\Interfaces\{CacheItemInterface, LoggerInterface, LoggerAwareInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerAwareTrait, CacheItemTrait};
use \DateTimeImmutable;

class CacheItem implements CacheItemInterface, LoggerAwareInterface
{
	use CacheItemTrait;
	use LoggerAwareTrait;

	private const CACHE_DIR = __DIR__ . DIRECTORY_SEPARATOR . '.cache' . DIRECTORY_SEPARATOR;

	private $_key = null;

	final public function __construct(string $key, ?LoggerInterface $logger = null)
	{
		if (isset($logger)) {
			$this->setLogger($logger);
		} else {
			$this->setLogger(new NullLogger());
		}

		$this->logger->debug('Cache key: {key}', ['key' => $key]);
		$this->_key = $key;
		$status = 'Miss';

		if (file_exists($this->_getPath())) {
			[$expires, $serialized] = json_decode(file_get_contents($this->_getPath()));

			if (isset($expires)) {
				$this->expiresAt(new DateTimeImmutable($expires));
			}

			if ($this->_isExpired()) {
				$status = 'Expired';
				$this->_unlink();
				$this->expiresAt(null);
			} else {
				$status = 'Hit';
				$this->_setSerialized($serialized);
			}
		}

		$this->logger->debug('Cache status: {status}', ['status' => $status]);
	}

	final public function __destruct() {
		$this->_save();
	}

	final public function __debugInfo(): array
	{
		return [
			'key'        => $this->getkey(),
			'serialized' => $this->_getSerialized(),
			'expires'    => $this->_getExpiresAsString(),
		];
	}

	/**
	 * Returns the key for the current cache item.
	 *
	 * The key is loaded by the Implementing Library, but should be available to
	 * the higher level callers when needed.
	 *
	 * @return string
	 *   The key string for this cache item.
	 */
	final public function getKey(): string
	{
		return $this->_key;
	}

	final private function _getPath():string
	{
		return self::CACHE_DIR . $this->getKey();
	}

	final private function _exists(): bool
	{
		return file_exists($this->_getPath());
	}

	final private function _unlink(): bool
	{
		if ($this->_exists()) {
			return unlink($this->_getPath());
		} else {
			return false;
		}
	}

	final private function _save(): bool
	{
		if ($this->_isExpired()) {
			return $this->_unlink();
		} elseif (! $this->_changed()) {
			return false;
		} elseif (! $this->_hasSerialized()) {
			return $this->_unlink();
		} else {
			return file_put_contents($this->_getPath(), json_encode([
				$this->_getExpiresAsString(),
				$this->_getSerialized(),
			])) !== false;
		}
	}
}
