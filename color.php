<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{Color as ColorInterface};

final class Color implements ColorInterface
{
	private $_red = 0;

	private $_green = 0;

	private $_blue = 0;

	private $_alpha = 0;

	final public function __construct(int $red, int $green, int $blue, float $alpha = 0)
	{
		$this->setRed($red);
		$this->setGreen($green);
		$this->setBlue($blue);
		$this->setAlpha($alpha);
	}

	final public function __toString(): string
	{
		return $this->hasAlpha() ? $this->getRGBA() : $this->getHex();
	}

	final public function jsonSerialize(): string
	{
		return $this->hasAlpha() ? $this->getRGBA() : $this->getHex();
	}

	final public function getHex(): string
	{
		return sprintf('#%02x%02x%02x', $this->getRed(), $this->getGreen(), $this->getBlue());
	}

	final public function getRGBA(): string
	{
		return sprintf('rgba(%d, %d, %d, %d)', $this->getRed(), $this->getGreen(), $this->getBlue(), $this->getAlpha() / 127);
	}

	final public function getRGB(): string
	{
		return sprintf('rgb(%d, %d, %d)', $this->getRed(), $this->getGreen(), $this->getBlue());
	}

	final public function getRed(): int
	{
		return $this->_red;
	}

	final public function setRed(int $val): void
	{
		$this->_red = max(0, min($val, 255));
	}

	final public function getGreen(): int
	{
		return $this->_green;
	}

	final public function setGreen(int $val): void
	{
		$this->_green = max(0, min($val, 255));
	}

	final public function getBlue(): int
	{
		return $this->_blue;
	}

	final public function setBlue(int $val): void
	{
		$this->_blue = max(0, min($val, 255));
	}

	final public function getAlpha(): int
	{
		return $this->_alpha;
	}

	final public function setAlpha(float $val): void
	{
		$this->_alpha = max(0, min(1, $val)) * 127;
	}

	final public function hasAlpha(): bool
	{
		return $this->getAlpha() !== 0;
	}

	final public function rgb(int $r, int $g, int $b): ColorInterface
	{
		return new self($r, $g, $b);
	}

	final public static function rgba(int $r, int $g, int $b, float $a): ColorInterface
	{
		return new self($r, $g, $b, $a);
	}

	final public static function hex(string $hex):? ColorInterface
	{
		$hex = ltrim($hex, '#');

		if (strlen($hex) === 6) {
			return new self(
				hexdec(substr($hex, 0, 2)),
				hexdec(substr($hex, 2, 2)),
				hexdec(substr($hex, 4, 2))
			);
		} elseif (strlen($hex) === 3) {
			return new self(
				hexdec(str_repeat(substr($hex, 0, 1), 2)),
				hexdec(str_repeat(substr($hex, 1, 1), 2)),
				hexdec(str_repeat(substr($hex, 2, 1), 2))
			);
		} else {
			return null;
		}
	}

	final public static function fromInt(int $color):? ColorInterface
	{
		$r = ($color >> 16) & 0xFF;
		$g = ($colorrgb >> 8) & 0xFF;
		$b = $color & 0xFF;

		if (isset($r, $g, $b)) {
			return new self($r, $g, $b);
		} else {
			return null;
		}
	}
}
