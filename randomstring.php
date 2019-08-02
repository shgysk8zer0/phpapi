<?php

namespace shgysk8zer0\PHPAPI;

final class RandomString
{
	private const _LOWER    = 'abcdefghijklmnopqrstuvwxyz';
	private const _UPPER    = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	private const _NUMERALS = '0123456789';
	private const _SPECIAL  = '~`!@#$%^&*()_-+=[{]}\\|;:\'",<.>/?';

	private $_length   = 0;
	private $_lower    = false;
	private $_upper    = false;
	private $_numerals = false;
	private $_special  = false;

	public function __construct(
		int  $length,
		bool $lower    = true,
		bool $upper    = true,
		bool $numerals = false,
		bool $special  = false
	)
	{
		if ($length <= 0) {
			throw new \InvalidArgumentException('Length must be greater than 0');
		} elseif (! $lower and ! $upper and ! $numerals and ! $special) {
			throw new \InvalidArgumentException('Must contain at least one set of characters');
		} else {
			$this->setLength($length);
			$this->setLower($lower);
			$this->setUpper($upper);
			$this->setNumerals($numerals);
			$this->setSpecial($special);
		}
	}

	public function __toString(): string
	{
		$chars    = '';
		$str      = '';
		$rand_num = mt_rand(5, 80);

		$length = $this->getLength();

		if ($this->getLower()) {
			$chars .= self::_LOWER;
		}

		if ($this->getUpper()) {
			$chars .= self::_UPPER;
		}

		if ($this->getNumerals()) {
			$chars .= self::_NUMERALS;
		}

		if ($this->getSpecial()) {
			$chars .= self::_SPECIAL;
		}

		$chars_len = strlen($chars) - 1;

		while (strlen($str) < $length) {
			$str .= substr($chars, mt_rand(0, $chars_len), 1);
		}


		for ($n = 0; $n < $rand_num; $n++) {
			$str = str_shuffle($str);
		}

		return $str;
	}

	public function saveAs(string $filename): bool
	{
		return file_put_contents($filename, $this, LOCK_EX);
	}

	public function setLength(int $length): void
	{
		$this->_length = $length;
	}

	public function getLength(): int
	{
		return $this->_length;
	}

	public function setLower(bool $include): void
	{
		$this->_lower = $include;
	}

	public function getLower(): bool
	{
		return $this->_lower;
	}

	public function setUpper(bool $include): void
	{
		$this->_upper = $include;
	}

	public function getUpper(): bool
	{
		return $this->_upper;
	}

	public function setNumerals(bool $include): void
	{
		$this->_numerals = $include;
	}

	public function getNumerals(): bool
	{
		return $this->_numerals;
	}

	public function setSpecial(bool $include): void
	{
		$this->_special = $include;
	}

	public function getSpecial(): bool
	{
		return $this->_special;
	}
}
