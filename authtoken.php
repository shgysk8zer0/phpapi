<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{User, PDO, PDOStatement, Headers, UUID, HTTPException};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \DateTime;
use \JSONSerializable;

class AuthToken implements JSONSerialzable;
{
	private $_pdo;

	private $_user;

	private $_uuid = null;

	private $_key = null;

	private $_permissions = [
		'name'          => true,
		'email'         => true,
		'image'         => true,
		'telephone'     => false,
		'readFiles'     => false,
		'writeFiles'    => false,
		'readPosts'     => false,
		'writePosts'    => false,
		'readComments'  => false,
		'writeComments' => false,
	];

	const ALGO = 'sha3-512';

	public function __construct(PDO $pdo = null, User $user = null)
	{
		if (isset($pdo)) {
			$this->setPdo($pdo);
		}

		if (isset($user)) {
			$this->setUser($user);
		}
	}

	public function __debugInfo(): array
	{
		return [
			'user'        => $this->getUser(),
			'permissions' => $this->getPermissions(),
		];
	}

	final public function jsonSerialize(): array
	{
		$data = [
			'algo'        => self::ALGO,
			'uuid'        => new UUID(),
			'user'        => $this->getUserUuid(),
			'generated'   => date(DateTime::W3C),
			'permissions' => $this->getPermissions(),
		];

		$key = hash(self::ALGO, new UUID());
		$data['hmac'] = hash_hmac(self::ALGO, json_encode($data), $key, false);
		return $data;
	}

	final public function __toString(): string
	{
		return base64_encode(json_encode($this));
	}

	final public function setPdo(PDO $pdo)
	{
		$this->_pdo = $pdo;
	}

	final private function _prepare(string $query): PDOStatement
	{
		return $this->_pdo->prepare($query);
	}

	final public function setUser($user): self
	{
		$this->_user = $user;
		return $this;
	}

	final public function getUser(): User
	{
		return $this->_user;
	}

	final public function getUserUuid(): string
	{
		return $this->getUser()->uuid;
	}

	final public function allow(string ...$perms): self
	{
		foreach($perms as $perm) {
			$this->{$perm} = true;
		}

		return $this;
	}

	final public function __isset(string $key): bool
	{
		return array_key_exists($key, $this->_permissions);
	}

	final public function __get(string $key): bool
	{
		return isset($this->{$key}) and $this->_permissions[$key] === true;
	}

	final public function __set(string $key, bool $allow)
	{
		if (isset($this->{$key})) {
			$this->_permissions[$key] = $allow;
		}
	}

	final public function can(string ...$perms): bool
	{
		$can = true;
		$permissions = $this->getPermissions();

		foreach($perms as $perm) {
			if ($this->{$perm} !== true) {
				$can = false;
				break;
			}
		}

		return $can;
	}

	final public function getPermissions(): array
	{
		return $this->_permissions;
	}

	final public function generate(string $algo = self::ALGO): string
	{
		$stm =  $this->_prepare('INSERT INTO `token` (`uuid`, `pass`) VALUES (:uuid, :key);');
		$data = [
			'algo'        => $algo,
			'uuid'        => new UUID(),
			'user'        => $this->getUserUuid(),
			'generated'   => date(DateTime::W3C),
			'permissions' => $this->getPermissions(),
		];

		$key = hash($algo, new UUID());
		$data['hmac'] = hash_hmac($algo, json_encode($data), $key, false);

		if ($stm->execute([':uuid' => $data['uuid'], ':key' => $key])) {
			return base64_encode(json_encode($data));
		} else {
			throw new HTTPException('Error saving token', HTTP::INTERNAL_SERVER_ERROR);
		}
	}

	final public function validate(string $req_token): bool
	{
		$token = json_decode(base64_decode($req_token));
		$stm = $this->_prepare('SELECT `pass` AS `key` FROM `token` WHERE `uuid` = :uuid LIMIT 1;');

		if ($stm->execute([':uuid' => $token->uuid])) {
			$match = $stm->fetchObject();
			$hmac = $token->hmac;
			unset($token->hmac);
			$expected = hash_hmac($token->algo, json_encode($token), $match->key, false);

			if (hash_equals($hmac, $expected)) {
				$user_stm = $this->_prepare('SELECT `id` FROM `users` WHERE `uuid` = :uuid LIMIT 1;');
				$user_stm->execute([':uuid' => $token->user]);
				$user = new User($this->_pdo);
				$user->setUser($user_stm->fetchObject()->id);
				$this->setUser($user);
				$this->_permissions = $token->permissions;
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
