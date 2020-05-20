<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\Traits\TemplateTrait;
use \InvalidArgumentException;

class Template
{
	use TemplateTrait;

	private const ESCAPE_FLAGS     = ENT_COMPAT | ENT_HTML5;

	private const USE_INCLUDE_PATH = false;

	private $_charset         = 'UTF-8';

	private $_html_escape     = true;

	private $_left_enclosure  = '{{';

	private $_right_enclosure = '}}';

	private $_nl_to_br        = false;

	private $_strip_comments  = true;

	private $_trim            = false;

	private $_filename        = null;

	public function __construct(
		string $filename,
		string $left_enclosure   = null,
		string $right_enclsure   = null,
		bool   $use_include_path = self::USE_INCLUDE_PATH,
		string $charset          = null,
		bool   $html_escape      = null,
		bool   $trim             = null,
		bool   $nl_to_br         = null,
		bool   $strip_comments   = null
	)
	{
		if (isset($charset))         $this->setCharset($charset);
		if (isset($html_escape))     $this->setHtmlEscape($html_escape);
		if (isset($trim))            $this->setTrim($trim);
		if (isset($nl_to_br))        $this->setNlToBr($nl_to_br);
		if (isset($strip_comments))  $this->setStripComments($strip_comments);

		// No setter methods for these because setting while in use wouldn't work out well
		if (isset($left_enclosure))  $this->_left_enclosure = $left_enclosure;
		if (isset($right_enclosure)) $this->_right_enclosure = $right_enclosure;

		if (! $this->_openFile($filename, $use_include_path)) {
			$this->_filename = $filename;
			throw new InvalidArgumentException(sprintf('Could not locate template file: "%s"', $filename));
		}
	}

	public function __debugInfo(): array
	{
		return [
			'filename' => $this->getFilename(),
			'data'     => $this->_getData(),
		];
	}

	public function __toString(): string
	{
		return $this->stringify($this->_strip_comments, $this->_trim, $this->_nl_to_br) . PHP_EOL;
	}

	final public function __isset(string $key): bool
	{
		return $this->has($key);
	}

	final public function __unset(string $key): void
	{
		$this->remove($key);
	}

	final public function __get(string $key):? string
	{
		return $this->get($key);
	}

	final public function __set(string $key, string $value): void
	{
		$this->set($key, $value, $this->_html_escape, $this->_charset, self::ESCAPE_FLAGS);
	}

	final public function getFilename():? string
	{
		return $this->_filename;
	}

	final public function setCharset(string $val): void
	{
		$this->_charset = $val;
	}

	final public function setHtmlEscape(bool $val): void
	{
		$this->_html_escape = $val;
	}

	final public function setNlToBr(bool $val): void
	{
		$this->_nl_to_br = $val;
	}

	final public function setStripComments(bool $val): void
	{
		$this->_strip_comments = $val;
	}

	final public function setTrim(bool $val): void
	{
		$this->_trim = $val;
	}

	final public function saveAs(string $filename): bool
	{
		return file_put_contents($filename, $this, LOCK_EX) !== false;
	}

	final protected function _convert(string $key): string
	{
		return $this->_left_enclosure . strtoupper($key) . $this->_right_enclosure;
	}
}
