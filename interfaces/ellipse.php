<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Interfaces\{Point as PointInterface};

interface Ellipse
{
	public function getCenter(): PointInterface;

	public function setCenter(PointInterface $val): void;

	public function getHeight(): float;

	public function setHeight(float $val): void;

	public function getWidth(): float;

	public function setWidth(float $val): void;
}
