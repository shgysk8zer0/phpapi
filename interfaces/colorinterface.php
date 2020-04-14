<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \JSONSerializable;

interface ColorInterface extends JSONSerializable
{
	public function getRGB(): string;

	public function getRGBA(): string;

	public function getHex(): string;

	public function getRed(): int;

	public function setRed(int $val): void;

	public function getGreen(): int;

	public function setGreen(int $val): void;

	public function getBlue(): int;

	public function setBlue(int $val): void;

	public function getAlpha(): int;

	public function setAlpha(float $val): void;

	public function hasAlpha(): bool;

	public function rgb(int $r, int $g, int $b): ColorInterface;

	public static function rgba(int $r, int $g, int $b, float $a): ColorInterface;

	public static function hex(string $hex):? ColorInterface;

	public static function fromInt(int $color):? ColorInterface;
}
