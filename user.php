<?php

namespace shgysk8zer0\PHPAPI;

use \DateTime;
use \PDO;
use \shgysk8zer0\PHPAPI\{Token, HTTPException, Headers, URL, UUID};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \shgysk8zer0\PHPAPI\Interfaces\{InputData};
use \shgysk8zer0\PHPAPI\Schema\{Person};
use \Exception;
use \InvalidArgumentException;
use \Throwable;
use \JsonSerializable;

final class User implements JsonSerializable
{
	const PASSWORD_ALGO = PASSWORD_DEFAULT;

	const PASSWORD_OPTS = [
		'cost' => 10,
	];

	const HASH_ALGO = 'sha3-256';

	private $_id       = null;

	private $_username = null;

	private $_person   = null;

	private $_role     = null;

	private $_created  = null;

	private $_updated  = null;

	private $_loggedIn = false;

	private $_hash     = null;

	private $_pdo      = null;

	private $_token    = null;

	private $_pwned    = null;

	private $_permissions = [];

	private static $_key = null;

	final public function __construct(PDO $pdo)
	{
		$this->_pdo = $pdo;
		Person::setPDO($pdo);
		$this->_person = new Person();
	}

	final public function __get(string $key)
	{
		switch($key) {
			case 'id': return $this->_id;
			case 'username': return $this->_username;
			case 'role': return $this->_role;
			case 'permissions': return $this->_permissions;
			case 'loggedIn': return $this->_loggedIn;
			case 'created': return $this->_created;
			case 'updated': return $this->_updated;
			case 'person': return $this->_person;
			case 'token':
				if (is_null($this->_token) and isset(static::$_key) and $this->loggedIn) {
					$token = new Token();
					$token->setId($this->_id);
					$token->setDate(new DateTime());
					$token->setKey(static::$_key);
					$this->_token = "{$token}";
				}
				return $this->_token;
			case 'pwned': return $this->_pwned;
			default:
				throw new InvalidArgumentException(sprintf('Undefined or invalid property: "%s"', $key));
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
				'role'     => $this->role,
				'token'    => $this->token,
				'created'  => $this->created->format(DateTime::W3C),
				'updated'  => $this->updated->format(DateTime::W3C),
				'loggedIn' => $this->loggedIn,
				'isAdmin'  => $this->isAdmin(),
				'pwned'    => $this->pwned,
				'permissions'    => $this->permissions,
				'person'   => $this->person,
			];
		} else {
			return [
				'id'       => null,
				'username' => null,
				'role'     => null,
				'token'    => null,
				'created'  => null,
				'updated'  => null,
				'loggedIn' => false,
				'isAdmin'  => false,
				'permissions'    => [],
				'person'   => $this->person,
			];
		}
	}

	final public function setUser(int $id): bool
	{
		$stm = $this->_pdo->prepare(
			'SELECT `Person`.`email` AS `username`,
				`users`.`password` AS `hash`,
				`users`.`created`,
				`users`.`updated`,
				`users`.`person`,
				`users`.`role`
			FROM `users`
			JOIN `Person` ON `users`.`person` = `Person`.`id`
			WHERE `users`.`id` = :id
			LIMIT 1;'
		);

		$perms_stm = $this->_pdo->prepare('SELECT * FROM `roles` WHERE `id` = :role LIMIT 1;');

		$stm->bindValue(':id', $id);

		if ($stm->execute() and $data = $stm->fetchObject()) {
			$perms_stm->execute([':role' => $data->role]);
			$perms = $perms_stm->fetchObject();
			$this->_role = $perms->name;
			$perms = get_object_vars($perms);
			unset($perms['id'], $perms['name']);
			$this->_id = $id;
			$this->_username = $data->username;
			$this->_created = new DateTime($data->created);
			$this->_updated = new DateTime($data->updated);
			$this->_person = new Person($data->person);
			$this->_hash = $data->hash;
			$this->_loggedIn = true;
			$this->_permissions = array_map(function(string $val): bool
			{
				return $val === '1';
			}, $perms);
			return true;
		} else {
			return false;
		}
	}

	final public function login(string $username, string $password): bool
	{
		$stm = $this->_pdo->prepare(
			'SELECT `users`.`id`,
				`users`.`password` AS `hash`
			FROM `Person`
			JOIN `users` ON `Person`.`id` = `users`.`person`
			WHERE `Person`.`email` = :username
			LIMIT 1;'
		);

		$stm->bindValue(':username', $username);

		if ($stm->execute()) {
			$user = $stm->fetchObject();
			if (isset($user->hash) and password_verify($password, $user->hash)) {
				if ($this->passwordNeedsUpdate()) {
					$this->changePassword($password);
				}
				$this->setUser($user->id);
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
			$this->_role = 'guest';
			$this->_perms = [];
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
		return $this->role === 'admin';
	}

	public function can(string ...$perms): bool
	{
		$can = true;

		foreach ($perms as $perm) {
			if ($this->_permissions[$perm] !== true) {
				$can = false;
				break;
			}
		}

		return $can;
	}

	final public function create(InputData $input): bool
	{
		$this->_pdo->beginTransaction();

		try {
			$user = $this->_pdo->prepare('INSERT INTO `users` (
				`person`,
				`password`
			) VALUES (
				:person,
				:password
			);');

			$img = $this->_pdo->prepare('INSERT INTO `ImageObject` (
				`identifier`,
				`url`,
				`width`,
				`height`,
				`encodingFormat`,
			) VALUES (
				:uuid,
				:url,
				:width,
				:height,
				:encodingFormat
			);');

			$url = new URL(sprintf('https://secure.gravatar.com/avatar/%s', md5($input->get('username'))));
			$url->searchParams->set('s', 64);
			$url->searchParams->set('d', 'mm');

			$person = $this->_pdo->prepare('INSERT INTO `Person` (
				`identifier`.
				`givenName`,
				`additionalName`,
				`familyName`,
				`email`,
				`birthDate`,
				`image`
			) VALUES (
				:uuid,
				:givenName,
				:additionalName,
				:familyName,
				:email,
				:birthDate,
				:image
			);');

			if (! $img->execute([
				':uuid'           => UUID::generate(),
				':url'            => "{$url}",
				':height'         => $url->searchParams->get('s'),
				':width'          => $url->searchParams->get('s'),
				':encodingFormat' => 'image/jpeg',
			])) {
				throw new HTTPException('Error creating user image', HTTP::INTERNAL_SERVER_ERROR);
			} elseif (! $person->execute([
				':uuid'           => UUID::generate(),
				':givenName'      => $input->get('givenName'),
				':additionalName' => $input->get('additionalName', true, null),
				':familyName'     => $input->get('familyName'),
				':email'          => $input->get('username'),
				':birthDate'      => $input->get('birthDate', true, null),
				':image'          => $this->_pdo->lastInsertId(),
			])) {
				throw new HTTPException('Error creating `Person`');
			}  elseif (! $user->execute([
				':password' => password_hash($input->get('password', false), self::PASSWORD_ALGO, self::PASSWORD_OPTS),
				':person'   => $this->_pdo->lastInsertId(),
			])) {
				throw new HTTPException('Error creating `user`');
			} elseif ($id = intval($this->_pdo->lastInsertId()) and $id === 0) {
				throw new HTTPException('Created `user` with `id` of 0 or null');
			} elseif (! $this->_pdo->commit()) {
				throw new HTTPException('Error commiting the `user` transaction');
			} else {
				$this->setUser($id);
				return true;
			}
		} catch (Throwable $e) {
			if ($this->_pdo->inTransaction()) {
				$this->_pdo->rollback();
			}

			throw $e;
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
