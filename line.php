<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{
	Point as PointInterface,
	Line as LineInterface,
	Polygon as PolygonInterface
};
use \shgysk8zer0\PHPAPI\{Polygon};

class Line implements LineInterface
{
	private $_from = null;

	private $_to = null;

	final public function __construct(PointInterface $from, PointInterface $to)
	{
		$this->setFrom($from);
		$this->setTo($to);
	}

	final public function getFrom(): PointInterface
	{
		return $this->_from;
	}

	final public function setFrom(PointInterface $val): void
	{
		$this->_from = $val;
	}

	final public function getTo(): PointInterface
	{
		return $this->_to;
	}

	final public function setTo(PointInterface $val): void
	{
		$this->_to = $val;
	}

	final public function toPolygon(PointInterface ...$pts): PolygonInterface
	{
		return new Polygon($this->getFrom(), $this->getTo(), ...$pts);
	}

	final public function getLength(): float
	{
		return hypot($this->getRun(), $this->getRise());
	}

	final public function getAngle(): float
	{
		return atan($this->getRise() / $this->getRun()) * 180 / M_PI;
	}

	final public function getRise(): float
	{
		return $this->getFrom()->getY() - $this->getTo()->getY();
	}

	final public function getRun(): float
	{
		return $this->getFrom()->getX() - $this->getTo()->getX();
	}
}
