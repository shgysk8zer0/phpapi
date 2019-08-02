<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{User, PDO, PDOStatement, Headers, UUID, HTTPException};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \DateTime;
use \JSONSerializable;

class AuthToken implements JSONSerializable
{
	private $_pdo;

	private $_user;

	private $_uuid = null;

	private $_key = null;

	private $_generated = null;

	private $_permissions = [
		'name'          => true,
		'email'         => true,
		'image'         => true,
		'telephone'     => false,
		'address'       => false,
		'readFiles'     => false,
		'writeFiles'    => false,
		'readPosts'     => false,
		'writePosts'    => false,
		'readComments'  => false,
		'writeComments' => false,
		'readEvents'    => false,
		'writeEvents'   => false,
	];

	public const ALGO = 'sha3-512';

	public const PERMISSIONS = [
		'telephone',
		'address',
		'readFiles',
		'writeFiles',
		'readPosts',
		'writePosts',
		'readComments',
		'writeComments',
		'readEvents',
		'writeEvents',
	];

	private const _TABLE = 'token';

	public function __construct(PDO $pdo = null, User $user = null)
	{
		$this->_generated = new DateTime();
		$this->_uuid = new UUID();
		$this->_key = hash(self::ALGO, new UUID());
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
			'uuid'        => $this->_uuid,
			'user'        => $this->getUserUuid(),
			'generated'   => $this->_generated->format(DateTime::W3C),
			'permissions' => $this->getPermissions(),
		];

		$data['hmac'] = hash_hmac(self::ALGO, json_encode($data), $this->_key, false);
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
		return "{$this}";
	}

	final public function save(): bool
	{
		$stm =  $this->_prepare(sprintf('INSERT INTO `%s` (`uuid`, `pass`) VALUES (:uuid, :key);', self::_TABLE));
		return $stm->execute([':uuid' => $this->_uuid, ':key' => $this->_key]);
	}

	final public function delete(): bool
	{
		$stm = $this->_pdo->prepare(sprintf('DELETE FROM `%s` WHERE `uuid` = :uuid LIMIT 1;', self::_TABLE));
		return $stm->execute([':uuid' => $this->_uuid]);
	}

	final public function validate(string $req_token): bool
	{
		$token = json_decode(base64_decode($req_token));
		$stm = $this->_prepare(sprint('SELECT `pass` AS `key` FROM `%s` WHERE `uuid` = :uuid LIMIT 1;', self::_TABLE));

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
				$this->_uuid = $token->uuid;
				$this->_key = $match->key;
				$this->_generated = new DateTime($token->generated);
				$this->_permissions = get_object_vars($token->permissions);
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
