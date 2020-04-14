<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Interfaces\{Point as PointInterface, Polygon as PolygonInterface};
use \Countable;
use \JSONSerializable;
interface Polygon extends JSONserializable, Countable
{
	public function __construct(PointInterface ...$pts);

	public function addPoints(PointInterface ...$pts): void;

	public function getPoints(): array;

	public function setPoints(PointInterface ...$val): void;

	public function toPointsArray(): array;
}
