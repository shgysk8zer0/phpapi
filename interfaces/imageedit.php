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
interface ImageEdit extends ImageInterface
{
	public function getCenter():? PointInterface;

	public function getVerticalCenter():? float;

	public function getHorizontalCenter():? float;

	public function arc(
		PointInterface $center,
		int            $width,
		int            $height,
		int            $start,
		int            $end,
		ColorInterface $color,
		int            $style  = IMG_ARC_PIE
	): bool;

	public function arcFilled(
		PointInterface $center,
		int            $width,
		int            $height,
		int            $start,
		int            $end,
		ColorInterface $color,
		int            $style  = IMG_ARC_PIE
	): bool;

	public function polygon(PolygonInterface $poly, ColorInterface $color = null): bool;

	public function polygonFilled(PolygonInterface $poly, ColorInterface $color = null): bool;

	public function rectangle(RectangleInterface $rect, ColorInterface $color): bool;

	public function rectangleFilled(RectangleInterface $rect, ColorInterface $color): bool;

	public function ellipse(EllipseInterface $ellipse, ColorInterface $color): bool;

	public function ellipseFilled(EllipseInterface $ellipse, ColorInterface $color): bool;

	public function circle(CircleInterface $circle, ColorInterface $color): bool;

	public function circleFilled(CircleInterface $circle, ColorInterface $color): bool;

	public function line(LineInterface $line, ColorInterface $color): bool;

	public function filter(int $filter, int... $args): bool;

	public function text(
		string          $text,
		string          $font,
		ColorInterface  $color,
		float           $size        = 16,
		PointInterface  $pt          = null,
		float           $angle       = 0,
		float           $linespacing = 1
	):? array;

	public function textBoundingBox(
		string $text,
		string $font,
		float  $size        = 16,
		float  $angle       = 0,
		float  $linespacing = 1
	):? object;

	public function colorAt(PointInterface $pt):? ColorInterface;
}
