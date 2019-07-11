<?php
namespace shgysk8zer0\PHPAPI\Schema;

use \DateTime;
class Duration
{
	private $_from = null;

	private $_thru = null;

	private $_format = DateTime::W3C;

	final public function __construct(DateTime $from = null, DateTime $thru = null)
	{
		if (isset($from)) {
			$this->setFrom($from);
		}

		if (isset($thru)) {
			$this->setThru($thru);
		}
	}

	final public function __toString(): string
	{
		return "{$this->_from->format($this->_format)} - {$this->_thru->format($this->_format)}";
	}

	final public function getFrom(): DateTime
	{
		return $this->_from;
	}

	final public function setFrom(DateTime $from)
	{
		$this->_from = $from;
	}

	final public function getThru(): DateTime
	{
		return $this->_thru;
	}

	final public function setThru(DateTime $thru)
	{
		$this->_thru = $thru;
	}

	final public function setFormat(string $format)
	{
		$this->_format = $format;
	}
}
