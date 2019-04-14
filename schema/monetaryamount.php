<?php
namespace shgysk8zer0\PHPAPI\Schema;

final class MonetaryAmount
{
	const DEFAULT_CURRENCY = 'USD';

	private $_value = 0;

	private $_currency = self::DEFAULT_CURRENCY;

	final public function __construct(float $value, string $currency = self::DEFAULT_CURRENCY)
	{
		$this->setValue($value);
		$this->setCurrency($currency);
	}

	final public function setCurrency(string $currency)
	{
		$this->_currency = $currency;
	}

	final public function getCurrency(): string
	{
		return strtoupper($this->_currency);
	}

	final public function getValue(): float
	{
		return $this->_value;
	}

	final public function setValue(float $value)
	{
		$this->_value = $value;
	}
}
