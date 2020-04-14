<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Interfaces\{Point as PointInterface, Ellipse as EllipseInterface};
use \JSONSerializable;

interface Circle extends JSONSerializable
{
	public function getCenter(): PointInterface;

	public function setCenter(PointInterface $val): void;

	public function getRadius(): float;

	public function setRadius(float $val): void;

	public function asEllipse(): EllipseInterface;
}
