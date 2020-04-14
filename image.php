<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{File, Point, Line, Polygon, Ellipse, Color};
use \shgysk8zer0\PHPAPI\Interfaces\{
	Point as PointInterface,
	Line as LineInterface,
	Polygon as PolygonInterface,
	Ellipse as EllipseInterface,
	Circle as CircleInterface,
	Rectangle as RectangleInterface,
	Color as ColorInterface
};
use \StdClass;
use \InvalidArgumentException;

/**
 * @SEE https://www.php.net/manual/en/book.image.php
 */
class Image
{
	private $_resource = null;

	final public function __destruct()
	{
		if ($this->loaded()) {
			imagedestroy($this->_resource);
		}
	}

	final public function __clone()
	{
		if ($this->loaded()) {
			$width = $this->getWidth();
			$height = $this->getHeight();
			$resource = imagecreatetruecolor($width, $height);
			imagecopy($resource, $this->_resource, 0, 0, 0, 0, $width, $height);
			$this->_setResource($resource);
		}
	}

	final public function __debugInfo(): array
	{
		return [
			'height' => $this->getHeight(),
			'width'  => $this->getWidth(),
		];
	}

	final public function getWidth():? int
	{
		if ($this->loaded()) {
			return imagesx($this->_resource);
		} else {
			return 0;
		}
	}

	final public function getHeight():? int
	{
		if ($this->loaded()) {
			return imagesy($this->_resource);
		} else {
			return null;
		}
	}

	final public function getUpperLeft():? PointInterface
	{
		if ($this->loaded()) {
			return new Point(0, 0);
		} else {
			return null;
		}
	}

	final public function getLowerLeft():? PointInterface
	{
		if ($this->loaded()) {
			return new Point(0, $this->getHeight());
		} else {
			return null;
		}
	}

	final public function getUpperRight():? PointInterface
	{
		if ($this->loaded()) {
			return new Point($this->getWidth(), 0);
		} else {
			return null;
		}
	}

	final public function getLowerRight():? PointInterface
	{
		if ($this->loaded()) {
		 	return new Point($this->getWidth(), $this->getHeight());
		} else {
			return null;
		}
	}

	final public function getCenter():? PointInterface
	{
		if ($this->loaded()) {
			return new Point($this->getHorizontalCenter(), $this->getVerticalCenter());
		}
	}

	final public function getMinDimension():? int
	{
		if ($this->loaded()) {
			return min($this->getWidth(), $this->getHeight());
		} else {
			return null;
		}
	}

	final public function getMaxDimension():? float
	{
		if ($this->loaded()) {
			return max($this->getWidth(), $this->getHeight());
		} else {
			return null;
		}
	}

	final public function getRatio():? float
	{
		if ($this->loaded()) {
			return $this->getWidth() / $this->getHeight();
		}
	}

	final public function getVerticalCenter():? float
	{
		if ($this->loaded()) {
			return $this->getHeight() / 2;
		} else {
			return null;
		}
	}

	final public function getHorizontalCenter():? float
	{
		if ($this->loaded()) {
			return $this->getWidth() / 2;
		} else {
			return null;
		}
	}

	final public function getCorners():? array
	{
		if ($this->loaded()) {
			$height = $this->getHeight();
			$width  = $this->getWidth();
			return [
				'lowerLeft'  => new Point(0, 0),
				'lowerRight' => new Point($width, 0),
				'upperRight' => new Point($width, $height),
				'upperLeft'  => new Point(0, $height),
			];
		} else {
			return null;
		}
	}

	final public function copy(
		self $img,
		?PointInterface $at = null,
		?PointInterface $from = null
	):? self
	{
		if ($this->loaded() and $img->loaded()) {
			$dest = clone $this;

			if (is_null($at)) {
				$at = new Point(0, 0);
			}

			if (is_null($from)) {
				$from = new Point(0, 0);
			}

			if (imagecopy($dest->_resource, $img->_resource, $at->getX(), $at->getY(), $from->getX(), $from->getY(), $img->getWidth(), $img->getHeight())) {
				return $dest;
			} else {
				return null;
			}
		} else {
			return null;
		}
	}

	final public function setThickness(int $val): bool
	{
		if ($this->loaded()) {
			return imagesetthickness($this->_resource, $val);
		} else {
			return false;
		}
	}

	final public function getTrueColor(): bool
	{
		return $this->loaded() and imageistruecolor($this->_resource);
	}

	final public function setTrueColor(bool $val = true): bool
	{
		if ($this->loaded()) {
			return imagepalettetotruecolor($this->_resource);
		} else {
			return false;
		}
	}

	final public function alphaBlending(bool $val = true): bool
	{
		if ($this->loaded()) {
			return imagealphablending($this->_resource, $val);
		} else {
			return false;
		}
	}

	final public function saveAlpha(bool $val = true): bool
	{
		if ($this->loaded()) {
			return imagesavealpha($this->_resource, $val);
		} else {
			return false;
		}
	}

	final public function antiAlias(bool $val = true): bool
	{
		if ($this->loaded()) {
			return imageantialias($this->_resource, $val);
		} else {
			return false;
		}
	}

	final public function interlace(bool $val = true): bool
	{
		if (! $this->loaded()) {
			return false;
		} elseif ($val) {
			return imageinterlace($this->_resource, true) === 1;
		} else {
			return imageinterlace($this->_resource, false) === 0;
		}
	}

	final public function interpolation(int $mode): bool
	{
		if ($this->loaded()) {
			return imagesetinterpolation($this->_resouce, $mode);
		} else {
			return false;
		}
	}

	final public function loaded(): bool
	{
		return is_resource($this->_resource);
	}

	final public function fill(ColorInterface $color, PointInterfae $from = null): bool
	{
		if ($this->loaded()) {
			if (is_null($from)) {
				$from = new Point(0, 0);
			}
			return imagefill($this->_resource, $from->getX(), $from->getY(), $this->_colorToInt($color));
		}
	}

	final public function rotate(
		float           $angle,
		?ColorInterface $color              = null,
		bool            $ignore_transparent = false
	):? self
	{
		if ($this->loaded()) {
			if (is_null($color)) {
				$color = new Color(0, 0, 0, 127);
			}
			$resource = imagerotate($this->_resource, $angle, $this->_colorToInt($color), $ignore_transparent ? 1 : 0);
			return static::createFromResource($resource);
		} else {
			return null;
		}
	}

	final public function flip(int $mode = IMG_FLIP_HORIZONTAL): bool
	{
		if ($this->loaded()) {
			return imageflip($this->_resource, $mode);
		} else {
			return false;
		}
	}

	final public function resize(
		int $width,
		int $height = -1,
		int $mode   = IMG_BICUBIC
	):? self
	{
		if ($this->loaded()) {
			$resource = imagescale($this->_resource, $width, $height, $mode);
			return static::createFromResource($resource);
		} else {
			return null;
		}
	}

	final public function arc(
		PointInterface $center,
		int            $width,
		int            $height,
		int            $start,
		int            $end,
		ColorInterface $color,
		int            $style  = IMG_ARC_PIE
	):? bool
	{
		if ($this->loaded()) {
			return imagearc($this->_resource, $center->getX(), $center->getY(),
				$width, $height, $start, $end, $this->_colorToInt($color), $style);
		} else {
			return false;
		}
	}

	final public function arcFilled(
		PointInterface $center,
		int            $width,
		int            $height,
		int            $start,
		int            $end,
		ColorInterface $color,
		int            $style  = IMG_ARC_PIE
	):? bool
	{
		if ($this->loaded()) {
			return imagefilledarc($this->_resource, $center->getX(), $center->getY(),
				$width, $height, $start, $end, $this->_colorToInt($color), $style);
		} else {
			return false;
		}
	}

	final public function polygon(PolygonInterface $poly, ColorInterface $color = null): bool
	{
		if ($this->loaded()) {
			if (is_null($color)) {
				$color = new Color(0, 0, 0);
			}
			return imagepolygon(
				$this->_resource,
				$poly->toPointsArray(),
				count($poly),
				$this->_colorToInt($color)
			);
		} else {
			return false;
		}
	}

	final public function polygonFilled(PolygonInterface $poly, ColorInterface $color = null): bool
	{
		if ($this->loaded()) {
			if (is_null($color)) {
				$color = new Color(0, 0, 0);
			}
			return imagefilledpolygon(
				$this->_resource,
				$poly->toPointsArray(),
				count($poly),
				$this->_colorToInt($color)
			);
		} else {
			return false;
		}
	}

	final public function rectangle(RectangleInterface $rect, ColorInterface $color): bool
	{
		if ($this->loaded()) {
			return imagerectangle($this->_resource, $rect->getFrom()->getX(), $rect->getFrom()->getY(),
				$rect->getTo()->getX(), $rect->getTo()->getY(), $this->_colorToInt($color));
		} else {
			return false;
		}
	}

	final public function rectangleFilled(RectangleInterface $rect, ColorInterface $color): bool
	{
		if ($this->loaded()) {
			return imagefilledrectangle($this->_resource, $rect->getFrom()->getX(), $rect->getFrom()->getY(),
				$rect->getTo()->getX(), $rect->getTo()->getY(), $this->_colorToInt($color));
		} else {
			return false;
		}
	}

	/**
	 * @SEE https://www.php.net/manual/en/function.imagefttext.php
	 */
	final public function text(
		string          $text,
		string          $font,
		ColorInterface  $color,
		float           $size        = 16,
		PointInterface  $pt          = null,
		float           $angle       = 0,
		float           $linespacing = 1
	):? array
	{
		if ($this->loaded()) {
			if (is_null($pt)) {
				$pt = new Point($size, $size);
			}

			$result = imagefttext($this->_resource, $size, $angle, $pt->getX(), $pt->getY(),
				$this->_colorToInt($color), $font, $text, ['linespacing' => $linespacing]);
			if (is_array($result)) {
				return [
					'lowerLeft'  => new Point($result[0], $result[1]),
					'lowerRight' => new Point($result[2], $result[3]),
					'upperRight' => new Point($result[4], $result[5]),
					'upperLeft'  => new Point($result[6], $result[7]),
				];
			} else {
				return [];
			}
		} else {
			return null;
		}
	}

	final public function textBoundingBox(
		string $text,
		string $font,
		float  $size        = 16,
		float  $angle       = 0,
		float  $linespacing = 1
	):? object
	{
		$results = imageftbbox($size, $angle, $font, $text, ['linespaceing' => $linespacing]);

		if (is_array($results)) {
			$dimensions = new StdClass();
			$dimensions->width  = $results[2] - $results[0];
			$dimensions->height = $results[1] - $results[5];
			return $dimensions;
		} else {
			return null;
		}
	}

	final public function ellipse(EllipseInterface $ellipse, ColorInterface $color): bool
	{
		if ($this->loaded()) {
			return imageellipse($this->_resource, $ellipse->getCenter()->getX(),
				$ellipse->getCenter()->getY(),
				$ellipse->getWidth(), $ellipse->getHeight(), $this->_colorToInt($color));
		} else {
			return false;
		}
	}

	final public function ellipseFilled(EllipseInterface $ellipse, ColorInterface $color): bool
	{
		if ($this->loaded()) {
			return imagefilledellipse($this->_resource, $ellipse->getCenter()->getX(),
				$ellipse->getCenter()->getY(),
				$ellipse->getWidth(), $ellipse->getHeight(), $this->_colorToInt($color));
		} else {
			return false;
		}
	}

	final public function circle(CircleInterface $circle, ColorInterface $color): bool
	{
		return $this->ellipse($circle->asEllipse(), $color);
	}

	final public function circleFilled(CircleInterface $circle, ColorInterface $color): bool
	{
		return $this->ellipseFilled($circle->asEllipse(), $color);
	}

	final public function line(Line $line, ColorInterface $color): bool
	{
		if ($this->loaded()) {
			return imageline($this->_resource, $line->getFrom()->getX(), $line->getFrom()->getY(),
				$line->getTo()->getX(), $line->getTo()->getY(), $this->_colorToInt($color));
		} else {
			return false;
		}
	}

	final public function filter(int $filter, int... $args): bool
	{
		if ($this->loaded()) {
			return imagefilter($this->_resource, $filter, ...$args);
		} else {
			return false;
		}
	}

	final public function invertColors(): bool
	{
		return $this->filter(IMG_FILTER_NEGATE);
	}

	final public function grayScale(): bool
	{
		return $this->filter(IMG_FILTER_GRAYSCALE);
	}

	final public function pixelate(int $size = 1): bool
	{
		return $this->filter(IMG_FILTER_PIXELATE, $size);
	}

	final public function brightness(int $level): bool
	{
		return $this->filter(IMG_FILTER_BRIGHTNESS, $level);
	}

	final public function contrast(int $level): bool
	{
		return $this->filter(IMG_FILTER_CONTRAST, $level);
	}

	final public function colorize(int $r, int $g, int $b, int $a = 0): bool
	{
		return $this->filter(IMG_FILTER_COLORIZE, $r, $g, $b, $a);
	}

	final public function edgeDetect(): bool
	{
		return $this->filter(IMG_FILTER_EDGEDETECT);
	}

	final public function emboss(): bool
	{
		return $this->filter(IMG_FILTER_EMBOSS);
	}

	final public function gaussianBlur(): bool
	{
		return $this->filter(IMG_FILTER_GAUSSIAN_BLUR);
	}

	final public function selectiveBlur(): bool
	{
		return $this->filter(IMG_FILTER_SELECTIVE_BLUR);
	}

	final public function smooth(int $level): bool
	{
		return $this->filter(IMG_FILTER_SMOOTH, $level);
	}

	final public function meanRemoval(): bool
	{
		return $this->filter(IMG_FILTER_MEAN_REMOVAL);
	}

	// @TODO implement scatter for PHP 7.4


	final public function colorAt(PointInterface $pt):? ColorInterface
	{
		if ($this->loaded()) {
			$rgb = imagecolorat($this->_resource, $pt->getX(), $pt->getY());

			return Color::fromInt($rgb);
		} else {
			return null;
		}
	}

	final public function saveAsGIF(?string $fname = null): bool
	{
		if ($this->loaded()) {
			return imagegif($this->_resource, $fname);
		} else {
			return false;
		}
	}

	final public function saveAsJPEG(?string $fname = null, int $quality = 80): bool
	{
		if ($this->loaded()) {
			return imagejpeg($this->_resource, $fname, $quality) ?? false;
		} else {
			return false;
		}
	}

	final public function saveAsPNG(
		?string $fname   = null,
		int     $quality = 80,
		int     $filters = PNG_NO_FILTER
	): bool
	{
		if ($this->loaded()) {
			return imagepng($this->_resource, $fname, $quality / 10, $filters) ?? false;
		} else {
			return false;
		}
	}

	final public function saveAsWebP(?string $fname = null, int $quality = 80): bool
	{
		if ($this->loaded()) {
			return imagewebp($this->_resource, $fname, $quality) ?? false;
		} else {
			return false;
		}
	}

	final private function _setResource($resource): bool
	{
		if (is_resource($resource)) {
			$this->_resource = $resource;
			return true;
		} else {
			return false;
		}
	}

	final private function _colorToInt(ColorInterface $color):? int
	{
		if (! $this->loaded()) {
			return null;
		} elseif ($color->hasAlpha()) {
			return imagecolorallocatealpha($this->_resource, $color->getRed(),
				$color->getGreen(), $color->getBlue(), $color->getAlpha());
		} else {
			return imagecolorallocate($this->_resource, $color->getRed(),
				$color->getGreen(), $color->getBlue());
		}
	}

	final public function crop(PointInterface $top_right, PointInterface $bottom_left):? self
	{
		if ($this->loaded() and $resource = imagecrop($this->_resource, [
			'x'      => $top_right->getX(),
			'y'      => $top_right->getY(),
			'width'  => $bottom_left->getX(),
			'height' => $bottom_left->getY(),
		])) {
			return static::createFromResource($resource);
		} else {
			return null;
		}
	}

	final public function autoCrop(
		int            $mode      = IMG_CROP_DEFAULT,
		float          $threshold = 0.5,
		ColorInterface $color     = null
	):? self
	{
		if (is_null($color)) {
			$color = new Color();
		}
		if ($this->loaded() and $resource = imageautocrop($this->_resource, $mode,
			$threshold, $this->_colorToInt($color))) {
			return static::createFromResource($resource);
		} else {
			return null;
		}
	}

	final public static function loadFromFile(string $fname):? self
	{
		$resouce = null;

		switch(exif_imagetype($fname)) {
			case IMAGETYPE_JPEG:
				$resource = imagecreatefromjpeg($fname);
				break;

			case IMAGETYPE_PNG:
				$resource = imagecreatefrompng($fname);
				break;

			case IMAGETYPE_WEBP:
				$resource = imagecreatefromwebp($fname);
				break;

			case IMAGETYPE_GIF:
				$resource = imagecreatefromgif($fname);
				break;

			default:
				return null;
		}

		return static::createFromResource($resource);
	}

	final public static function create(int $width, int $height, bool $truecolor = true):? self
	{
		if ($truecolor) {
			$resource = imagecreatetruecolor($width, $height);
		} else {
			$resouce = imagecreate($width, $height);
		}

		return static::createFromResource($resource);
	}

	final public static function createFromResource($val = null):? self
	{
		if (is_resource($val)) {
			$img = new self();
			$img->_setResource($val);
			return $img;
		} else {
			return null;
		}
	}

	final public static function loadFromUpload(File $file):? self
	{
		if (! $file->hasError()) {
			return static::loadFromFile($file->tmpName);
		} else {
			return null;
		}
	}

	final public static function getFontPath():? string
	{
		return getenv('GDFONTPATH') ?? null;
	}
	final public static function rgb(int $red, int $green, int $blue): ColorInterface
	{
		return new Color($red, $green, $blue);
	}

	final public static function rgba(int $red, int $green, int $blue, float $alpha = 0): ColorInterface
	{
		return new Color($red, $green, $blue, $alpha);
	}

	final public static function hex(string $hex):? ColorInterface
	{
		return Color::hex($hex);
	}

	final public static function setFontPath(string $dir): bool
	{
		if (is_dir($dir)) {
			return putenv('GDFONTPATH=' . realpath($dir));
		} else {
			return false;
		}
	}
}
