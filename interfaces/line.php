<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Interfaces\{Point as PointInterface, Polygon as PolygonInterface};

interface Line
{
	public function getFrom(): PointInterface;

	public function setFrom(Point $val): void;

	public function getTo(): Point;

	public function setTo(Point $val): void;

	public function toPolygon(PointInterface ...$pts): PolygonInterface;

	public function getLength(): float;

	public function getAngle(): float;

	public function getRise(): float;

	public function getRun(): float;
}
