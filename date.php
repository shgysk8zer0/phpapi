<?php
namespace shgysk8zer0\PHPAPI;

use \DateTimeImmutable;
use \JSONSerializable;

/**
 * Class extending DateTimeImmutable with W3C formatted dates for string & JSON
 */
class Date extends DateTimeImmutable implements JSONSerializable
{
	protected const FORMAT = self::W3C;

	final public function __toString(): string
	{
		return $this->format(self::FORMAT);
	}

	final public function jsonSerialize(): string
	{
		return $this->format(self::FORMAT);
	}
}
