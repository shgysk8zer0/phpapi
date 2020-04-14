<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{Point as PointInterface, Rectangle as RectangleInterface};

class Rectangle implements RectangleInterface
{
	private $_from = null;

	private $_to = null;

	final public function __construct(PointInterface $from, PointInterface $to)
	{
		$this->setFrom($from);
		$this->setTo($to);
	}

	final public function jsonSerialize(): array
	{
		return [
			'from' => $this->getFrom(),
			'to'   => $this->getTo(),
		];
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
}
