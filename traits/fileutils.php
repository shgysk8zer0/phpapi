<?php
namespace shgysk8zer0\PHPAPI\Traits;
use \shgysk8zer0\PHPAPI\{HTTPException, Headers};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};

trait FileUtils
{
	private $_file_path = '';

	final public function isUploadFile(): bool
	{
		Headers::set('X-Path', $this->_file_path);
		return is_uploaded_file($this->_file_path);
	}

	final public function exists(): bool
	{
		return $this->_file_path !== '' && @file_exists($this->_file_path);
	}

	final public function saveAs(string $path, $allow_override = false, $perms = 0666): bool
	{
		if (! $this->exists()) {
			return false;
		}
		$dir = dirname($path);
		if (! $allow_override and @file_exists($path)) {
			throw new HTTPException('File already exists', HTTP::CONFLICT);
		} elseif (! file_exists($dir) and ! mkdir($dir, $perms)) {
			throw new HTTPException('Upload directory does not exist', HTTP::INTERNAL_SERVER_ERROR);
		} elseif (is_uploaded_file($this->_file_path) and @move_uploaded_file($this->_file_path, $path)) {
			$this->_setFilePath($path);
			return true;
		} elseif (@rename($this->_file_path, $path)) {
			$this->_setFilePath($path);
			return true;
		} else {
			return false;
		}
	}

	final public function md5(): string
	{
		return $this->hash('md5');
	}

	final public function sha(): string
	{
		return $this->hash('sha1');
	}

	final public function hash(string $algo = self::DEFAULT_HASH_ALGO, bool $raw_output = false): string
	{
		return $this->exists() ? hash_file($algo, $this->_file_path, $raw_output) : '';
	}

	final public function hmac(string $key, string $algo = self::DEFAULT_HASH_ALOG, bool $raw_output = false): string
	{
		return $this->exists() ? hash_hmac_file($algo, $this->_file_path, $key, $raw_output) : '';
	}

	final public function matchesHash(string $hash, string $algo = self::DEFAULT_HASH_ALGO, bool $raw_output): bool
	{
		return hash_equals($hash, $this->hash($algo, $raw_output));
	}

	final public function matchesHmac(string $hmac, string $key, string $algo = self::DEFAULT_HASH_ALOG, bool $raw_output = false): bool
	{
		return hash_equals($hmac, $this->hmac($key, $algo, $raw_output));
	}

	final protected function _setFilePath(string $path)
	{
		$this->_file_path = trim($path);
	}

	final protected function _getFilePath(): string
	{
		return $this->_file_path;
	}
}
