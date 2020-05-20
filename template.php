<?php
namespace shgysk8zer0\PHPAPI;

use \InvalidArgumentException;

class Template
{
	private const L = '{';

	private const R = '}';

	public const CHARSET          = 'UTF-8';

	public const ESCAPE_FLAGS     = ENT_COMPAT | ENT_HTML5;

	public const HTML_ESCAPE      = true;

	public const STRIP_COMMENTS   = false;

	public const USE_INCLUDE_PATH = false;

	public const TRIM             = false;

	public const NL_TO_BR         = false;

	private $_content        = '';

	private $_charset        = self::CHARSET;

	private $_html_escape    = self::HTML_ESCAPE;

	private $_nl_to_br       = self::NL_TO_BR;

	private $_strip_comments = self::STRIP_COMMENTS;

	private $_trim           = self::TRIM;

	private $_data           = [];

	private $_filename       = null;

	public function __construct(
		string $filename,
		bool   $use_include_path = self::USE_INCLUDE_PATH,
		string $charset          = null,
		bool   $html_escape      = null,
		bool   $trim             = null,
		bool   $nl_to_br         = null,
		bool   $strip_comments   = null
	)
	{
		if (isset($charset))        $this->setCharset($charset);
		if (isset($html_escape))    $this->setHtmlEscape($html_escape);
		if (isset($trim))           $this->setTrim($trim);
		if (isset($nl_to_br))       $this->setNlToBr($nl_to_br);
		if (isset($strip_comments)) $this->setStripComments($strip_comments);

		if (! $this->_open($filename, $use_include_path)) {
			throw new InvalidArgumentException(sprintf('Could not locate template file: "%s"', $filename));
		}
	}

	final public function __debugInfo(): array
	{
		return [
			'filename' => $this->_filename,
			'data'     => $this->_data,
		];
	}

	final public function __toString(): string
	{
		$content = strtr($this->_content, $this->_data);

		if ($this->_strip_comments) {
			$content = $this->_removeComments($content);
		}

		if ($this->_trim) {
			$content = str_replace(["\n", "\r", "\t"], [null, null, null], $content);
		}

		if ($this->_nl_to_br) {
			$content = nl2br($content);
		}

		return trim($content) . PHP_EOL;
	}

	final public function __isset(string $key): bool
	{
		return array_key_exists($this->_convert($key), $this->_data);
	}

	final public function __unset(string $key): void
	{
		unset($this->_data[$this->_convert($key)]);
	}

	final public function __get(string $key):? string
	{
		if (isset($this->{$key})) {
			return $this->_data[$this->_convert($key)];
		} else {
			return null;
		}
	}

	final public function __set(string $key, string $value): void
	{
		if ($this->_html_escape) {
			$this->_data[$this->_convert($key)] = htmlentities($value, self::ESCAPE_FLAGS, $this->_charset);
		} else {
			$this->_data[$this->_convert($key)] = $value;
		}
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

	final private function _convert(string $key): string
	{
		return self::L . strtoupper($key) . self::R;
	}

	final protected function _open(string $filename, bool $use_include_path = false): bool
	{
		$this->_filename = $filename;
		$content = @file_get_contents($filename, $use_include_path);

		if (is_string($content)) {
			$this->_content = $content;
			return true;
		} else {
			return false;
		}
	}

	final private function _removeComments(string $content) {
		return preg_replace('/<!--(.|\s)*?-->/', '', $content);
	}
}
