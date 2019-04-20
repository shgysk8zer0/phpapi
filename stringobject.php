<?php
namespace shgysk8zer0\PHPAPI;

// @see https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/String
class StringObject implements \JSONSerializable
{
	private $_str = '';

	final public function __construct(string $str = '')
	{
		$this->_str = $str;
	}

	final public function __toString(): string
	{
		return $this->_str;
	}

	final public function __debugInfo(): array
	{
		return ['string' => "{$this}"];
	}

	final public function __get(string $prop)
	{
		switch($prop) {
			case 'length': return strlen($this);
		}
	}

	final public function jsonSerialize(): string
	{
		return "{$this}";
	}

	final public function concat(string ...$strs): self
	{
		return new self($this . join($strs, null));
	}

	final public function replace(string $search, string $replace): self
	{
		return new self(str_replace($search, $replace, $this));
	}

	final public function includes(string $search, int $pos = 0): bool
	{
		if ($pos + strlen($search) > $this->length) {
			return false;
		} else {
			return $this->indexOf($search, $pos) !== -1;
		}
	}

	final public function startsWith(string $search, int $pos = 0): bool
	{
		return "{$this->substring($pos, $pos + strlen($search))}" === $search;
	}

	final public function endsWith(string $search, int $length = null): bool
	{
		if (isset($length)) {
			return $this->substring(0, $length)->endsWith($search);
		} else {
			return substr($this, $this->length - strlen($search)) === $search;
		}
	}

	final public function indexOf(string $search, int $from = 0): int
	{
		$pos = strpos($this, $search, $from);
		if ($pos === false) {
			return -1;
		} else {
			return $pos;
		}
	}

	// @TODO This doesn't work correctly with `$length` set
	final public function lastIndexOf(string $search, int $from = 0): int
	{
		if ($from < 0) {
			$from = 0;
		}
		if ($search === '') {
			return $from;
		} elseif ($from !== 0) {
			return $this->substring($from)->lastIndexOf($search);
		} else {
			$pos = strrpos($this, $search, $from);
			if ($pos === false) {
				return -1;
			} else {
				return $pos;
			}
		}
	}

	final public function substring(int $start, int $end = null): self
	{
		if (isset($end) and $start > $end) {
			list($start, $end) = [$end, $start];
		}
		return new self(isset($end) ? substr($this, $start, $end - $start) : substr($this, $start));
	}

	final public function split(string $separator, int $limit = PHP_INT_MAX): array
	{
		return explode($separator, $this, $limit);
	}

	final public function padStart(int $length, string $pad_string = ' '): self
	{
		return new self(str_pad($this, $length, $pad_string, STR_PAD_LEFT));
	}

	final public function padEnd(int $length, string $pad_string = ' '): self
	{
		return new self(str_pad($this, $length, $pad_string, STR_PAD_RIGHT));
	}

	final public function trim(): self
	{
		return new self(trim($this));
	}

	final public function trimStart(): self
	{
		return new self(ltrim($this));
	}

	final public function trimEnd(): self
	{
		return new self(rtrim($this));
	}

	final public function repeat(int $count = 0): self
	{
		return new self(str_repeat($this, $count));
	}

	final public function toLowerCase(): self
	{
		return new self(strtolower($this));
	}

	final public function toUpperCase(): self
	{
		return new self(strtoupper($this));
	}
}
