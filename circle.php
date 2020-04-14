<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{Ellipse};
use \shgysk8zer0\PHPAPI\Interfaces\{
	Point as PointInterface,
	Ellipse as EllipseInterface,
	Circle as CircleInterface
};

class Circle implements CircleInterface
{
	private $_center = null;

	private $_radius = 0;

	final public function __construct(PointInterface $center, float $radius)
	{
		$this->setCenter($center);
		$this->setRadius($radius);
	}

	public function jsonSerialize(): array
	{
		return [
			'center' => $this->getCenter(),
			'radius' => $this->getRadius(),
		];
	}

	final public function getCenter(): PointInterface
	{
		return $this->_center;
	}

	final public function setCenter(PointInterface $val): void
	{
		$this->_center = $val;
	}

	final public function getRadius(): float
	{
		return $this->_radius;
	}

	final public function setRadius(float $val): void
	{
		$this->_radius = $val;
	}

	final public function asEllipse(): EllipseInterface
	{
		return new Ellipse($this->getCenter(), $this->getRadius(), $this->getRadius());
	}
}
