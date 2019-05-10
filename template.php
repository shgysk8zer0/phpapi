<?php
namespace shgysk8zer0\PHPAPI;

final class Template
{
	const CHARSET = 'UTF-8';

	private $_data       = [];
	private $_fname      = '';
	private $_contents   = '';
	private $_prefix     = '';
	private $_suffix     = '';
	private $_capitalize = false;

	private static $_instances    = [];
	private static $_template_dir = './';
	private static $_ext          = 'html';

	/**
	 * [load description]
	 * @param  string $fname [description]
	 * @return self          [description]
	 */
	final public static function load(string $fname): self
	{
		if (! array_key_exists($fname, static::$_instances)) {
			static::$_instances[$fname] = new self($fname);
		}
		return static::$_instances[$fname];
	}

	final public static function setTemplateDir(string $dir): bool
	{
		if (@is_dir($dir)) {
			static::$_template_dir = realpath($dir) . DIRECTORY_SEPARATOR;
			return true;
		} else {
			return false;
		}
	}

	final public static function setExtension(string $ext)
	{
		static::$_ext = $ext;
	}

	/**
	 * [__construct description]
	 * @param string  $fname            [description]
	 * @param string  $prefix           [description]
	 * @param string  $suffix           [description]
	 * @param boolean $capitalize       [description]
	 * @param boolean $use_include_path [description]
	 */
	final public function __construct(
		string $fname,
		string $prefix           = '{{',
		string $suffix           = '}}',
		bool   $capitalize       = false
	)
	{
		$this->_fname      = $fname;
		$this->_prefix     = $prefix;
		$this->_suffix     = $suffix;
		$this->_capitalize = $capitalize;
		$this->_contents   = file_get_contents(static::$_template_dir . "{$fname}." . static::$_ext);
	}

	/**
	 * [__get description]
	 * @param  string $key [description]
	 * @return [type]      [description]
	 */
	final public function __get(string $key): string
	{
		return $this->get($key);
	}

	/**
	 * [__set description]
	 * @param string $key   [description]
	 * @param string $value [description]
	 */
	final public function __set(string $key, string $value)
	{
		$this->set($key, $value);
	}

	/**
	 * [__isset description]
	 * @param  string  $key [description]
	 * @return boolean      [description]
	 */
	final public function __isset(string $key): bool
	{
		return $this->has($key);
	}

	/**
	 * [__unset description]
	 * @param string $key [description]
	 */
	final public function __unset(string $key)
	{
		$this->remove($key);
	}

	final public function __call(string $key, array $values): self
	{
		$this->__set($key, $values[0]);
		return $this;
	}

	/**
	 * [__toString description]
	 * @return string [description]
	 */
	final public function __toString(): string
	{
		return str_replace(
			array_keys($this->_data),
			array_values($this->_data),
			$this->_contents
		);
	}

	/**
	 * [__debugInfo description]
	 * @return array [description]
	 */
	final public function __debugInfo(): array
	{
		return [
			'fname'      => $this->_fname,
			'directory'  => static::$_template_dir,
			'extension'  => static::$_ext,
			'prefix'     => $this->_prefix,
			'suffix'     => $this->_suffix,
			'capitalize' => $this->_capitalize,
			'data'       => $this->_data,
		];
	}

	final public function get(string $key): string
	{
		return $this->_data[$this->_convertKey($key)] ?? '';
	}

	final public function set(
		string $key,
		string $value,
		bool   $escape  = true,
		string $charset = self::CHARSET
	): self
	{
		if ($escape) {
			$this->_data[$this->_convertKey($key)] = htmlspecialchars($value, ENT_COMPAT | ENT_HTML5, $charset);
		} else {
			$this->_data[$this->_convertKey($key)] = $value;
		}
		return $this;
	}

	final public function has(string $key): bool
	{
		return array_key_exists($this->_convertKey($key), $this->_data);
	}

	final public function remove(string $key)
	{
		unset($this->_data[$this->_convertKey($key)]);
	}

	/**
	 * [reset description]
	 * @return bool [description]
	 */
	final public function reset(): bool
	{
		$empty = empty($this->_data);
		$this->_data = [];
		return ! $empty;
	}

	/**
	 * [_convertKey description]
	 * @param  string $key [description]
	 * @return string      [description]
	 */
	final private function _convertKey(string $key): string
	{
		return $this->_capitalize ? strtoupper("{$this->_prefix}{$key}{$this->_suffix}")
			: "{$this->_prefix}{$key}{$this->_suffix}";
	}
}
