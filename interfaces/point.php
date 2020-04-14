<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Interfaces\{Line as LineInterface, Circle as CircleInterface};
use \JSONSerializable;

interface Point extends JSONSerializable
{
	public function getX(): float;

	public function setX(float $val): void;

	public function getY(): float;

	public function setY(float $val): void;

	public function modify(float $x, float $y): Point;

	public function modifyX(float $x): Point;

	public function modifyY(float $y): Point;

	public function circleAt(float $r): CircleInterface;

	public function lineTo(Point $pt): LineInterface;

	public function distanceTo(Point $pt): float;
}
