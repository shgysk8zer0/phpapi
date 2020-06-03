<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Interfaces\CacheItemInterface;
use \PDO;
use \PDOStatement;
use \DateTimeInterface;
use \DateTimeImmutable;

trait PDOCacheItemTrait
{
	use CacheItemTrait;

	private $_pdo = null;

	final public function setPDO(PDO $pdo): void
	{
		$this->_pdo = $pdo;
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
	abstract public function getKey(): string;

	/**
	 * Retrieves the value of the item from the cache associated with this object's key.
	 *
	 * The value returned must be identical to the value originally stored by set().
	 *
	 * If isHit() returns false, this method MUST return null. Note that null
	 * is a legitimate cached value, so the isHit() method SHOULD be used to
	 * differentiate between "null value was found" and "no value was found."
	 *
	 * @return mixed
	 *   The value corresponding to this cache item's key, or null if not found.
	 */
	final public function get():? object
	{
		if ($stm = $this->_prepare('SELECT `value`,
				DATE_FORMAT(`expires`, "%Y-%m-%dT%TZ") AS `expires`
				 FROM `Cache`
				 WHERE `key` = :key
				 LIMIT 1;')
		) {
			$stm->execute(['key' => $this->getKey()]);

			if ($result = $stm->fetchObject()) {
				print_r($result);
				return $result;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	/**
	 * Confirms if the cache item lookup resulted in a cache hit.
	 *
	 * Note: This method MUST NOT have a race condition between calling isHit()
	 * and calling get().
	 *
	 * @return bool
	 *   True if the request resulted in a cache hit. False otherwise.
	 */
	final public function isHit(): bool
	{
		if ($stm = $this->_prepare('SELECT COUNT(*) AS `found` FROM `Cache` WHERE `key` = :key LIMIT 1;')) {
			$stm->execute(['key' => $this->getkey()]);
			$result = $stm->fetchObject();
			var_dump($result);
			return $result->found === '1';
		} else {
			return false;
		}
	}

	/**
	 * Sets the value represented by this cache item.
	 *
	 * The $value argument may be any item that can be serialized by PHP,
	 * although the method of serialization is left up to the Implementing
	 * Library.
	 *
	 * @param mixed $value
	 *   The serializable value to be stored.
	 *
	 * @return static
	 *   The invoked object.
	 */
	final public function set(object $value): CacheItemInterface
	{
		if ($stm = $this->_prepare('INSERT INTO `Cache` (
				`key`,
				`value`,
				`expires`
			) VALUES (:key, :value, :expires)
			ON DUPLIATE KEY UPDATE
				`value` = :value,
				`expires`= COALESCE(:expires, NULL);')) {
			$stm->bindValue(':key', $this->getKey());
			$stm->bindValue(':value', serialize($value));

			if (isset($this->_expires)) {
				$stm->bindValue(':expires', $this->_expires->format(DateTimeInterface::W3C));
			}

			$stm->execute();
		}

		return $this;
	}

	private function _prepare(string $sql):? PDOStatement
	{
		if (isset($this->_pdo)) {
			return $this->_pdo->prepare($sql);
		} else {
			return null;
		}
	}
}
