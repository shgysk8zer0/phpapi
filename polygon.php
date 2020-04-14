<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\{Point};
use \shgysk8zer0\PHPAPI\Interfaces\{Point as PointInterface, Polygon as PolygonInterface};
use \Countable;
use \JSONSerializable;

class Polygon implements PolygonInterface
{
	private $_pts = [];

	final public function __construct(PointInterface ...$pts)
	{
		$this->setPoints(...$pts);
	}

	final public function jsonSerialize(): array
	{
		return $this->getPoints();
	}

	final public function addPoints(PointInterface ...$pts): void
	{
		$this->setPoint(...array_merge($pts, $this->getPoints()));
	}

	final public function getPoints(): array
	{
		return $this->_pts;
	}

	final public function setPoints(PointInterface ...$val): void
	{
		$this->_pts = $val;
	}

	final public function toPointsArray(): array
	{
		return array_reduce($this->getPoints(), function(array $pts, Point $pt): array
		{
			$pts[] = $pt->getX();
			$pts[] = $pt->getY();
			return $pts;
		}, []);
	}

	final public function count(): int
	{
		return count($this->_pts);
	}
}
