<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Interfaces\{
	Point as PointInterface,
	Line as LineInterface,
	Polygon as PolygonInterface,
	Ellipse as EllipseInterface,
	Circle as CircleInterface,
	Rectangle as RectangleInterface,
	Color as ColorInterface,
	Image as ImageInterface
};

use \StdClass;

interface Image
{
	public function __destruct();

	public function __clone();

	public function loaded(): bool;

	public function getWidth():? int;

	public function getHeight():? int;

	public function getTrueColor(): bool;

	public function setTrueColor(bool $val = true): bool;

	public function alphaBlending(bool $val = true): bool;

	public function saveAlpha(bool $val = true): bool;

	public function antiAlias(bool $val = true): bool;

	public function interlace(bool $val = true): bool;

	public function interpolation(int $mode): bool;

	public function saveAsGIF(?string $fname = null): bool;

	public function saveAsJPEG(?string $fname = null, int $quality = 80): bool;

	public function saveAsPNG(
		?string $fname   = null,
		int     $quality = 80,
		int     $filters = PNG_NO_FILTER
	): bool;

	public function saveAsWebP(?string $fname = null, int $quality = 80): bool;



	public function fill(ColorInterface $color, PointInterface $from = null): bool;

	public function copy(
		Image           $img,
		?PointInterface $at = null,
		?PointInterface $from = null
	):? ImageInterface;

	public function rotate(
		float           $angle,
		?ColorInterface $color              = null,
		bool            $ignore_transparent = false
	):? ImageInterface;

	public function flip(int $mode = IMG_FLIP_HORIZONTAL): bool;

	public function resize(
		int $width,
		int $height = -1,
		int $mode   = IMG_BICUBIC
	):? ImageInterface;

	public function crop(PointInterface $top_right, PointInterface $bottom_left):? ImageInterface;

	public function autoCrop(
		int            $mode      = IMG_CROP_DEFAULT,
		float          $threshold = 0.5,
		ColorInterface $color     = null
	):? ImageInterface;

	public static function loadFromFile(string $fname):? ImageInterface;

	public static function create(int $width, int $height, bool $truecolor = true):? ImageInterface;

	public static function createFromResource($val = null):? ImageInterface;

	public static function getFontPath():? string;

	public static function setFontPath(string $dir): bool;
}
