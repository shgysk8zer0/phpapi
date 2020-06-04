<?php

namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{
	LoggerAwareInterface,
	CacheInterface,
	LoggerInterface,
};

use \shgysk8zer0\PHPAPI\Traits\{
	CacheTrait,
	LoggerAwareTrait,
	PDOAwareTrait,
	PDOCacheTrait,
};

use \DateTimeImmutable;
use \DateInterval;
use \StdClass;
use \PDO;

class MySQLCache implements CacheInterface, LoggerAwareInterface
{
	use LoggerAwareTrait;
	use PDOAwareTrait;
	use PDOCacheTrait;
	use CacheTrait;

	final public function __construct(?PDO $pdo = null, ?LoggerInterface $logger = null)
	{
		if (isset($pdo)) {
			$this->setPDO($pdo);
		}

		if (isset($logger)) {
			$this->setLogger($logger);
		} else {
			$this->setLogger(new NullLogger());
		}
	}

	/**
	 * Fetches a value from the cache.
	 *
	 * @param string $key	 The unique key of this item in the cache.
	 * @param mixed  $default Default value to return if the key does not exist.
	 *
	 * @return mixed The value of the item from the cache, or $default in case of cache miss.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function get(string $key, ?object $default = null):? object
	{
		if ($stm = $this->_prepare("SELECT `{$this->getColumn('key')}` AS `key`,
				`{$this->getColumn('value')}` AS `value`,
				`{$this->getColumn('expires')}` AS `expires`
			FROM `{$this->getTable()}`
			WHERE `{$this->getColumn('key')}` = :key
			LIMIT 1;", 'getter')) {
			$stm->bindValue(':key', $key);

			if ($stm->execute() and $result = $stm->fetchObject()) {
				if (isset($result->expires)) {
					$result->expires = new DateTimeImmutable("@{$result->expires}");
				}

				if (is_null($result->expires) or $result->expires > date('now')) {
					return unserialize($result->value);
				} else {
					$this->logger->info('Expired cache for {key}', ['key' => $key]);
					$this->delete($key);
					return $default;
				}
			} else {
				return $default;
			}
		} else {
			return $default;
		}
	}

	/**
	 * Persists data in the cache, uniquely referenced by a key with an optional expiration TTL time.
	 *
	 * @param string				 $key   The key of the item to store.
	 * @param mixed				  $value The value of the item to store. Must be serializable.
	 * @param null|\DateInterval $ttl   Optional. The TTL value of this item. If no value is sent and
	 *									  the driver supports TTL then the library may set a default value
	 *									  for it or let the driver take care of that.
	 *
	 * @return bool True on success and false on failure.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function set(string $key, object $value, ?DateInterval $ttl = null): bool
	{
		if ($stm = $this->_prepare("INSERT INTO `{$this->getTable()}` (
				`{$this->getColumn('key')}`,
				`{$this->getColumn('value')}`,
				`{$this->getColumn('expires')}`,
				`{$this->getColumn('created')}`,
				`{$this->getColumn('modified')}`
			) VALUES (
				:key,
				:value,
				:expires,
				:created,
				:updated
			) ON DUPLICATE KEY UPDATE
				`{$this->getColumn('value')}` = :value,
				`{$this->getColumn('expires')}` = :expires;",
		'setter')) {
			$date = new DateTimeImmutable();
			$stm->bindValue(':key', $key);
			$stm->bindValue(':value', serialize($value));
			$stm->bindValue(':created', $date->format(DateTimeImmutable::W3C));
			$stm->bindValue(':modified', $date->format(DateTimeImmutable::W3C));

			if (isset($ttl)) {
				$stm->bindValue(':expires', $date->add($ttl)->format(DateTimeImmutable::W3C));
			}

			return $stm->execute();
		} else {
			return false;
		}
	}

	/**
	 * Delete an item from the cache by its unique key.
	 *
	 * @param string $key The unique cache key of the item to delete.
	 *
	 * @return bool True if the item was successfully removed. False if there was an error.
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function delete(string $key): bool
	{
		if ($this->has($key) and $stm = $this->_prepare("DELETE FROM `{$this->getTable()}`
			WHERE `{$this->getColumn('key')}` = :key LIMIT 1;",
		'delete')) {
			$stm->bindValue(':key', $key);
			return $stm->execute() and $stm->rowCount() > 0;
		} else {
			return false;
		}
	}

	/**
	 * Wipes clean the entire cache's keys.
	 *
	 * @return bool True on success and false on failure.
	 */
	public function clear(): bool
	{
		if ($stm = $this->_prepare("DELETE FROM `{$this->getTable()}`;")) {
			return $stm->execute();
		} else {
			return false;
		}
	}

	public function clearExpired(): bool
	{
		if ($stm = $this->_prepare("DELETE FROM `{$this->getTable()}`
			WHERE `{$this->getColumn('expires')}` < now();"
		)) {
			return $stm->execute();
		} else {
			return false;
		}
	}

	public function getEntries(): array
	{
		if ($stm = $this->_prepare("SELECT `{$this->getColumn('key')}` AS `key`,
			DATE_FORMAT(`{$this->getColumn('expires')}`, '%Y-%m-%dT%TZ') AS `expires`,
			DATE_FORMAT(`{$this->getColumn('created')}`, '%Y-%m-%dT%TZ') AS `created`,
			DATE_FORMAT(`{$this->getColumn('modiofied')}`, '%Y-%m-%dT%TZ') AS `modified`
			FROM `{$this->getTable()}`;"
		)) {
			if ($stm->execute()) {
				return array_map(function(object $entry): object
				{
					if (isset($entry->expires)) {
						$entry->expires = new DateTimeImmutable($entry->expires);
					}
					$entry->created = new DateTimeImmutable($entry->created);
					$entry->modified = new DateTimeImmutable($entry->modified);

					return $entry;
				}, $stm->fetchAll(PDO::FETCH_CLASS));
			} else {
				return [];
			}
		} else {
			return [];
		}
	}

	/**
	 * Determines whether an item is present in the cache.
	 *
	 * NOTE: It is recommended that has() is only to be used for cache warming type purposes
	 * and not to be used within your live applications operations for get/set, as this method
	 * is subject to a race condition where your has() will return true and immediately after,
	 * another script can remove it, making the state of your app out of date.
	 *
	 * @param string $key The cache item key.
	 *
	 * @return bool
	 *
	 * @throws \InvalidArgumentException
	 *   MUST be thrown if the $key string is not a legal value.
	 */
	public function has(string $key): bool
	{
		if ($stm = $this->_prepare("SELECT COUNT(*) AS `matches`
			FROM `{$this->getTable()}`
			WHERE `{$this->getColumn('key')}` = :key
			AND ({$this->getColumn('expires')}` IS NULL OR {$this->getColumn('expires')}` > now());",
		'has')) {
			$stm->bindValue(':key', $key);

			if ($stm->execute() and $result = $stm->fetchObject()) {
				return intval($result->matches) > 0;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}

