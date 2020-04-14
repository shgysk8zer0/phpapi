<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPGeo\Interfaces\{
	GeoPointInterface,
	GeoLineInterface,
	GeoPolygonInterface,
	GeoEllipseInterface,
	GeoCircleInterface,
	GeoRectangleInterface,
};

use \StdClass;
interface ImageEditInterface extends ImageInterface
{
	public function getCenter():? GeoPointInterface;

	public function getVerticalCenter():? float;

	public function getHorizontalCenter():? float;

	public function arc(
		GeoPointInterface $center,
		int               $width,
		int               $height,
		int               $start,
		int               $end,
		ColorInterface    $color,
		int               $style  = IMG_ARC_PIE
	): bool;

	public function arcFilled(
		GeoPointInterface $center,
		int               $width,
		int               $height,
		int               $start,
		int               $end,
		ColorInterface    $color,
		int               $style  = IMG_ARC_PIE
	): bool;

	public function polygon(GeoPolygonInterface $poly, ColorInterface $color = null): bool;

	public function polygonFilled(GeoPolygonInterface $poly, ColorInterface $color = null): bool;

	public function rectangle(GeoRectangleInterface $rect, ColorInterface $color): bool;

	public function rectangleFilled(GeoRectangleInterface $rect, ColorInterface $color): bool;

	public function ellipse(GeoEllipseInterface $ellipse, ColorInterface $color): bool;

	public function ellipseFilled(GeoEllipseInterface $ellipse, ColorInterface $color): bool;

	public function circle(GeoCircleInterface $circle, ColorInterface $color): bool;

	public function circleFilled(GeoCircleInterface $circle, ColorInterface $color): bool;

	public function line(GeoLineInterface $line, ColorInterface $color): bool;

	public function filter(int $filter, int... $args): bool;

	public function text(
		string             $text,
		string             $font,
		ColorInterface     $color,
		float              $size        = 16,
		GeoPointInterface  $pt          = null,
		float              $angle       = 0,
		float              $linespacing = 1
	):? array;

	public function textBoundingBox(
		string $text,
		string $font,
		float  $size        = 16,
		float  $angle       = 0,
		float  $linespacing = 1
	):? object;

	public function colorAt(GeoPointInterface $pt):? ColorInterface;
}
