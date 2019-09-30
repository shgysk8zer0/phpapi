<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{HTTPException};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \DateTime;

final class Token implements \JSONSerializable
{
	public const HASH_ALGO = 'sha3-512';

	private $_id = 0;

	private $_date = null;

	private $_expires = 15;

	private $_expires_units = 'min';

	private $_key = '';

	final public function __toString(): string
	{
		$json = json_encode($this);
		$hmac = hash_hmac(self::HASH_ALGO, $json, $this->_key, false);
		$data = json_decode($json);
		$data->hmac = $hmac;
		return base64_encode(json_encode($data));
	}

	final public function jsonSerialize(): array
	{
		return [
			'id' => $this->getId(),
			'date' => $this->getDate()->format(DateTime::W3C),
			'expires' => $this->getExpires()->format(DateTime::W3C),
		];
	}

	final public function getId(): int
	{
		return $this->_id;
	}

	final public function setId(int $id): self
	{
		$this->_id = $id;
		return $this;
	}

	final public function getDate(): DateTime
	{
		return $this->_date instanceof DateTime ? $this->_date : new DateTime();
	}

	final public function setDate(DateTime $date): self
	{
		$this->_date = $date;
		return $this;
	}

	final public function getExpires(): DateTime
	{
		$expires = clone($this->getDate());
		$expires->modify("+ {$this->_expires} {$this->_expires_units}");
		return $expires;
	}

	final public function setExpires(int $value, string $units = 'min'): self
	{
		$this->_expires = $value;
		$this->_expires_units = $units;
		return $this;
	}

	final public function setKey(string $key, string $algo = self::HASH_ALGO): self
	{
		$this->_key = hash($algo, $key);
		return $this;
	}

	final static function validate(string $token, string $key): int
	{
		$json = @base64_decode($token);
		$data = @json_decode($json);

		if (is_object($data) and isset($data->hmac, $data->expires, $data->date)) {
			$now = time();
			$hmac = $data->hmac;
			$expires = strtotime($data->expires);
			$date = strtotime($data->date);
			$key = hash(self::HASH_ALGO, $key);
			unset($data->hmac);
			$gen_hmac = hash_hmac(self::HASH_ALGO, json_encode($data), $key, false);
			$match = hash_equals($gen_hmac, $hmac);
			$valid_dates = ($now >= $date and $now < $expires);

			if ($valid_dates and $match) {
				return $data->id;
			} else {
				trigger_error("Invalid token/HMAC. Given {$hmac} calculate {$gen_hmac}");
				return 0;
			}
		} else {
			throw new HTTPException('Invalid token', HTTP::UNAUTHORIZED);
		}
	}
}
