<?php
namespace shgysk8zer0\PHPAPI;

use \RuntimeException;
use \Exception;
use \InvalidArgumentException;
use \JSONSerializable;

class UploadFile implements JSONSerializable
{
	private static $_host = '';
	private $_uploadPath  = null;
	private $_path        = null;
	private $_size        = 0;
	private $_name        = null;
	private $_type        = null;

	final public function __construct(string $key)
	{
		if (! array_key_exists($key, $_FILES)) {
			throw new InvalidArgumentException("Undefined file: {$key}");
		} elseif (! array_key_exists('tmp_name', $_FILES[$key]) || $_FILES[$key]['tmp_name'] === '') {
			throw new Exception("Invalid file: {$key}");
		} elseif (! is_uploaded_file($_FILES[$key]['tmp_name'])) {
			header('Content-Type: application/json');
			exit(json_encode($_FILES[$key]));
			throw new Exception("Invalid upload: {$_FILES[$key]['tmp_name']}");
		} elseif ($_FILES[$key]['error'] !== UPLOAD_ERR_OK) {
			switch ($_FILES[$key]['error']) {
				case UPLOAD_ERR_NO_FILE:
					throw new RuntimeException('No file uploaded');
					break;
				case UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					throw new RuntimeException('Exceeded filesize limit');
					break;
				default:
					throw new RuntimeException('Unknown upload error');
			}
		} else {
			$file = $_FILES[$key];
			$this->_uploadPath = $file['tmp_name'];
			$this->_name = basename($file['name']);
			$this->_size = $file['size'];
			$this->_type = $file['type'] !== ''
				? $file['type']
				: mime_content_type($this->_uploadPath);
		}
	}

	final public function __toString(): string
	{
		return $this->isMoved() ? $this->_path : $this->_uploadPath;
	}

	final public function __get(string $key)
	{
		switch(strtolower($key)) {
			case 'name': return $this->_name;
			case 'hash':
			case 'md5': return $this->getHash();
			case 'size': return $this->_size;
			case 'path': return $this->_path;
			case 'type': return $this->_type;
			case 'moved': return $this->isMoved();
			case 'extension': return pathinfo($this->_name, PATHINFO_EXTENSION);
			case 'uploadPath': return $this->_uploadPath;
			case 'url': return $this->getUrl();
		}
	}

	final public function jsonSerialize(): array
	{
		return [
			'name'       => $this->_name,
			'type'       => $this->_type,
			'extension'  => $this->extension,
			'size'       => $this->_size,
			'url'        => $this->isMoved() ? $this->getUrl() : null,
			'hash'       => $this->getHash(),
			'saved'      => $this->isMoved(),
		];
	}

	final public function isMoved(): bool
	{
		return isset($this->_path);
	}

	final public function getUrl(string $host = ''): string
	{
		if ($host === '') {
			$host = static::$_host;
		}
		if ($this->isMoved()) {
			$path = str_replace($_SERVER['DOCUMENT_ROOT'], null, $this->_path);
			if (DIRECTORY_SEPARATOR !== '/') {
				$path = preg_replace('/' . preg_escape(DIRECTORY_SEPARATOR, '/') . '/', '/', $path);
			}
			return $host . $path;
		} else {
			return null;
		}
	}

	final public function getHash(): string
	{
		return md5_file($this, false);
	}

	final public function hashFileName(): string
	{
		return "{$this->hash}.{$this->extension}";
	}

	final public function moveTo(string $path): bool
	{
		$dir = dirname($path);
		if (! is_dir($dir) and ! mkdir($dir, 0774, true)) {
			throw new Exception(sprintf('%s does not exist and cannot be created', $dir));
		} elseif (move_uploaded_file($this->_uploadPath, $path)) {
			$this->_path = $path;
			return true;
		} else {
			return false;
		}
	}

	final public static function setHost(string $host): void
	{
		static::$_host = $host;
	}
}
