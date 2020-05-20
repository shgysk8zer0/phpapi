<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait TemplateTrait
{
	private $_content = '';

	private $_data    = [];

	final public function has(string $key): bool
	{
		return array_key_exists($this->_convert($key), $this->_data);
	}

	final public function remove(string $key): void
	{
		unset($this->_data[$this->_convert($key)]);
	}

	final public function get(string $key):? string
	{
		if ($this->has($key)) {
			return $this->_data[$this->_convert($key)];
		} else {
			return $this->_data[$this->_convert($key)];
		}
	}

	final public function set(
		string $key,
		string $value,
		bool   $html_escape = true,
		string $charset     = 'UTF-8',
		int    $flags       = ENT_COMPAT | ENT_HTML5
	): void
	{
		if ($html_escape) {
			$this->_data[$this->_convert($key)] = htmlentities($value, $flags, $charset);
		} else {
			$this->_data[$this->_convert($key)] = $value;
		}
	}

	final public function stringify(bool $strip_comments = false, bool $trim = false, bool $nl_to_br): string
	{
		$content = strtr($this->_content, $this->_data);

		if ($strip_comments) {
			$content = $this->_stripComments($content);
		}

		if ($trim) {
			$content = str_replace(["\n", "\r", "\t"], [null, null, null], $content);
		}

		if ($nl_to_br) {
			$content = nl2br($content);
		}

		return trim($content);
	}

	final protected function _setContent(string $content): void
	{
		$this->_content = trim($content);
	}

	final protected function _getData(): array
	{
		return $this->_data;
	}

	final protected function _openFile(string $filename, bool $use_include_path = false): bool
	{
		if ($content = @file_get_contents($filename, $use_include_path)) {
			$this->_setContent($content);
			return true;
		} else {
			return false;
		}
	}

	final protected function _stripComments(string $content) {
		return preg_replace('/<!--(.|\s)*?-->/', '', $content);
	}

	abstract protected function _convert(string $key): string;
}
