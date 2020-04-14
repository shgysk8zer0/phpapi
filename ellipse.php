<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{Point as PointInterface, Ellipse as EllipseInterface};

final class Ellipse implements EllipseInterface
{
	private $_center = null;

	private $_width = 0;

	private $_height = 0;

	final public function __construct(PointInterface $center, float $width, float $height)
	{
		$this->setCenter($center);
		$this->setWidth($width);
		$this->setHeight($height);
	}

	final public function getCenter(): PointInterface
	{
		return $this->_center;
	}

	final public function setCenter(PointInterface $val): void
	{
		$this->_center = $val;
	}

	final public function getHeight(): float
	{
		return $this->_height;
	}

	final public function setHeight(float $val): void
	{
		$this->_height = $val;
	}

	final public function getWidth(): float
	{
		return $this->_width;
	}

	final public function setWidth(float $val): void
	{
		$this->_width = $val;
	}
}
