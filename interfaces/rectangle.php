<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Interfaces\{Point as PointInterface};

interface Rectangle
{
	public function getFrom(): PointInterface;

	public function setFrom(PointInterface $val): void;

	public function getTo(): PointInterface;

	public function setTo(PointInterface $val): void;
}
