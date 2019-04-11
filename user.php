<?php

namespace shgysk8zer0\PHPAPI;

use \DateTime;
use \PDO;
use \shgysk8zer0\PHPAPI\{Token, HTTPException};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};

final class User implements \JsonSerializable
{
	const PASSWORD_ALGO = PASSWORD_DEFAULT;

	const PASSWORD_OPTS = [
		'cost' => 10,
	];

	const HASH_ALGO = 'sha3-256';

	private $_id       = null;

	private $_username = null;

	private $_created  = null;

	private $_updated  = null;

	private $_loggedIn = false;

	private $_hash     = null;

	private $_pdo      = null;

	private $_token    = null;

	private $_pwned    = null;

	private static $_key = null;

	final public function __construct(PDO $pdo)
	{
		$this->_pdo = $pdo;
	}

	final public function __get(string $key)
	{
		switch($key) {
			case 'id':
				return $this->_id;
			case 'username':
				return $this->_username;
			case 'loggedIn':
				return $this->_loggedIn;
			case 'created':
				return $this->_created;
			case 'updated':
				return $this->_updated;
			case 'token':
				if (is_null($this->_token) and isset(static::$_key) and $this->loggedIn) {
					$token = new Token();
					$token->setId($this->_id);
					$token->setDate(new DateTime());
					$token->setKey(static::$_key);
					$this->_token = "{$token}";
				}
				return $this->_token;
			case 'pwned':
				return $this->_pwned;
			default:
				throw new \InvalidArgumentException(sprintf('Undefined or invalid property: "%s"', $key));
		}
	}

	final public function __debugInfo(): array
	{
		return [
			'id'       => $this->id,
			'username' => $this->username,
			'created'  => $this->created,
			'updated'  => $this->updated,
			'loggedIn' => $this->loggedIn,
			'hash'     => $this->hash,
			'pwned'    => $this->pwned,
		];
	}

	public function __toString(): string
	{
		return $this->_username;
	}

	public function jsonSerialize(): array
	{
		if ($this->loggedIn) {
			return [
				'id'       => $this->id,
				'username' => $this->username,
				'token'    => $this->token,
				'created'  => $this->created->format(DateTime::W3C),
				'updated'  => $this->updated->format(DateTime::W3C),
				'loggedIn' => $this->loggedIn,
				'isAdmin'  => $this->isAdmin(),
				'pwned'    => $this->pwned,
			];
		} else {
			return [
				'id'       => null,
				'username' => null,
				'token'    => null,
				'created'  => null,
				'updated'  => null,
				'loggedIn' => false,
				'isAdmin'  => false,
			];
		}
	}

	final public function setUser(int $id): bool
	{
		$stm = $this->_pdo->prepare(
			'SELECT `id`, `username`, `password` AS `hash`, `created`, `updated`
			FROM `users`
			WHERE `id` = :id
			LIMIT 1;'
		);
		$stm->bindValue(':id', $id);

		if ($stm->execute()) {
			$data = $stm->fetchObject();
			if (! isset($data->id)) {
				return false;
			} else {
				$this->_id = intval($data->id);
				$this->_username = $data->username;
				$this->_created = new DateTime($data->created);
				$this->_updated = new DateTime($data->updated);
				$this->_hash = $data->hash;
				$this->_loggedIn = true;
				return true;
			}
		} else {
			return false;
		}
	}

	final public function login(string $username, string $password): bool
	{
		$stm = $this->_pdo->prepare(
			'SELECT `id`,
				`password` AS `hash`,
				`created`,
				`updated`
			FROM `users`
			WHERE `username` = :username
			LIMIT 1;'
		);

		$stm->bindValue(':username', $username);

		if ($stm->execute()) {
			$user = $stm->fetchObject();
			if (isset($user->hash) and password_verify($password, $user->hash)) {
				$this->_pwned = static::haveIBeenPwned($password);
				$this->_username = $username;
				$this->_created = new DateTime($user->created);
				$this->_updated = new DateTime($user->updated);
				$this->_loggedIn = true;
				$this->_id = intval($user->id);
				$this->_hash = $user->hash;

				if ($this->passwordNeedsUpdate()) {
					$this->changePassword($password);
				}
				return true;
			} else {
				$this->logout();
				return false;
			}
		} else {
			return false;
		}
	}

	final public function logout(): bool
	{
		if ($this->_loggedIn) {
			$this->_id = null;
			$this->_username = null;
			$this->_hash = null;
			$this->_created = null;
			$this->_updated = null;
			$this->_loggedIn = false;
			return true;
		} else {
			return false;
		}
	}

	final public function isLoggedIn(): bool
	{
		return $this->_loggedIn;
	}

	final public function isAdmin(): bool
	{
		return $this->id === 1;
	}

	final public function create(string $username, string $password): bool
	{
		$stm = $this->_pdo->prepare(
			'INSERT INTO `users` (
				`username`,
				`password`
			) VALUES (
				:username,
				:password
			);'
		);

		$hash = password_hash($password, self::PASSWORD_ALGO, self::PASSWORD_OPTS);
		$datetime = new DateTime('now');
		$stm->bindValue(':username', $username);
		$stm->bindValue(':password', $hash);

		try {
			$stm->execute();
			$id = intval($this->_pdo->lastInsertId());

			if ($id !== 0) {
				$this->_id = $id;
				$this->_username = $username;
				$this->_created = new DateTime();
				$this->_loggedIn = true;
				$this->_updated = $this->_created;
				$this->_hash = $hash;
				return true;
			} else {
				return false;
			}
		} catch (\Throwable $e) {
			return false;
		}
	}

	final public function passwordNeedsUpdate(): bool
	{
		return $this->_loggedIn
			&& password_needs_rehash($this->_hash, self::PASSWORD_ALGO, self::PASSWORD_OPTS);
	}

	final public function changePassword(string $password): bool
	{
		if ($this->_loggedIn) {
			$hash = password_hash($password, self::PASSWORD_ALGO, self::PASSWORD_OPTS);
			$stm = $this->_pdo->prepare(
				'UPDATE `users`
				SET `password` = :hash
				WHERE `id` = :id
				LIMIT 1;'
			);
			$stm->bindValue(':id', $this->_id);
			$stm->bindValue(':hash', $hash);
			$stm->execute();

			if ($stm->rowCount() === 1) {
				$this->_hash = $hash;
				$this->_updated = new DateTime();
				return true;
			} else {
				return false;
			}
		} else {
			throw new HTTPException('Cannot change password when not logged in', HTTP::UNAUTHORIZED);
		}
	}

	final public function delete(): bool
	{
		if ($this->_loggedIn) {
			$stm = $this->_pdo->prepare(
				'DELETE FROM `users`
				WHERE `id` = :id
				LIMIT 1;'
			);
			$stm->bindValue(':id', $this->_id);
			$stm->execute();
			return $stm->rowCount() === 1;
		} else {
			return false;
		}
	}

	final static public function getUser(PDO $pdo = null, Int $id): self
	{
		if (is_null($pdo)) {
			$pdo = PDO::load();
		}
		$user = new self($pdo);
		$user->setUser($id);
		return $user;
	}

	final public static function setKey(string $key)
	{
		static::$_key = $key;
	}

	final public static function haveIBeenPwned(string $pwd): bool
	{
		$hash   = strtoupper(sha1($pwd));
		$prefix = substr($hash, 0, 5);
		$rest   = substr($hash, 5);
		$req    = new Request("https://api.pwnedpasswords.com/range/{$prefix}");
		$resp   = $req->send();

		if ($resp->ok) {
			return strpos($resp->body, "{$rest}:") !== false;
		} else {
			return false;
		}
	}

	final static public function loadFromToken(PDO $pdo, string $token): self
	{
		if (isset(static::$_key)) {
			$id = token::validate($token, static::$_key);
			$user = new self($pdo);

			if ($id !== 0) {
				$user->setUser($id);
				$user->_token = $token;
			}
			return $user;
		} else {
			throw new \Exception('No key set');
		}
	}
}
