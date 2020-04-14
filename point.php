<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{Line};
use \shgysk8zer0\PHPAPI\Interfaces\{
	Point as PointInterface,
	Line as LineInterface,
	Circle as CircleInterface
};
use \InvalidArgumentException;

class Point implements PointInterface, Interfaces\StringifyInterface
{
	private $_x = 0;

	private $_y = 0;

	final public function __construct(int $x = 0, int $y = 0)
	{
		if (isset($x)) {
			$this->setX($x);
		}

		if (isset($y)) {
			$this->setY($y);
		}
	}

	final public function __toString(): string
	{
		return sprintf('%d, %d', $this->getX(), $this->getY());
	}

	final public function jsonSerialize(): array
	{
		return [
			$this->getX(),
			$this->getY(),
		];
	}

	final public function __debugInfo(): array
	{
		return [
			'x' => $this->getX(),
			'y' => $this->getY(),
		];
	}

	final public function __get(string $name):? float
	{
		switch($name) {
			case 'x':
				return $this->getX();

			case 'y':
				return $this->getY();

			default:
				throw new InvalidArgumentException(sprintf('Undefined coorinate: %s', $name));
				return null;
		}
	}

	final public function __set(string $name, float $value): void
	{
		switch($name) {
			case 'x':
				$this->setX($value);
				break;

			case 'y':
				$this->setY($value);
				break;

			default:
				throw new InvalidArgumentException(sprintf('Undefined coorinate: %s', $name));
		}
	}

	final public function getX(): float
	{
		return $this->_x;
	}

	final public function setX(float $val): void
	{
		$this->_x = $val;
	}

	final public function getY(): float
	{
		return $this->_y;
	}

	final public function setY(float $val): void
	{
		$this->_y = $val;
	}

	final public function modify(float $x, float $y): PointInterface
	{
		return new self($this->getX() + $x, $this->getY() + $y);
	}

	final public function modifyX(float $x): PointInterface
	{
		return $this->modify($x, 0);
	}

	final public function modifyY(float $y): PointInterface
	{
		return $this->modify(0, $y);
	}

	final public function lineTo(PointInterface $pt): LineInterface
	{
		return new Line($this, $pt);
	}

	final public function distanceTo(PointInterface $pt): float
	{
		return $this->lineTo($pt)->getLength();
	}

	final public function circleAt(float $r): CircleInterface
	{
		return new Circle($this, $r);
	}

	final public static function create(float ...$coords):? PointInterface
	{
		if (count($coords) > 1) {
			return new self(...$pts);
		}
	}
}
