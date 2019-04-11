<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{URL};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};

class File implements \JSONSerializable
{
	use Traits\FileUtils;
	private $_name = '';
	private $_tmp_name = '';
	private $_size = 0;
	private $_error = null;
	private $_type = '';
	private $_location = '';

	const DEFAULT_HASH_ALGO = 'md5';

	final public function __construct(string $key)
	{
		if (array_key_exists($key, $_FILES)) {
			$this->_parse($_FILES[$key]);
		}
	}

	final public function __get(string $key)
	{
		switch($key) {
			case 'name': return $this->_name ?? '';
			case 'tmpName': return $this->_tmp_name ?? '';
			case 'size': return $this->_size ?? 0;
			case 'type': return $this->_type;
			case 'error': return $this->_error;
			case 'path': return $this->_path;
			case 'saved': return $this->valid() && ! is_uploaded_file($this->tmpName);
			case 'hash': return $this->valid() ? $this->{self::DEFAULT_HASH_ALGO} : null;
			case 'md5': return $this->valid() ? $this->md5() : null;
			case 'ext': return $this->valid() ? pathinfo($this->name, PATHINFO_EXTENSION) : null;
			case 'location': return $this->valid() ? $this->_getFilePath() : '';
			case 'sha':
			case 'sha1': return $this->valid() ? $this->sha() : null;
			case 'url':
				if (! is_uploaded_file($this->tmpName)) {
					$scheme = (array_key_exists('HTTPS', $_SERVER) and ! empty($_SERVER['HTTPS'])) ? 'https://' : 'http://';
					return new URL(str_replace($_SERVER['DOCUMENT_ROOT'], null, $this->location), "{$scheme}{$_SERVER['HTTP_HOST']}");
				} else {
					return null;
				}
			case 'maxUploadSize':
				$upload_max_filesize = ini_get('upload_max_filesize') ?? 0;
				$post_max_size = ini_get('post_max_size') ?? 0;
				return intval($upload_max_filesize) > intval($post_max_size) ? "{$post_max_size}b" : "{$upload_max_filesize}b";
		}
	}

	final public function __toString(): string
	{
		return $this->saved ? $this->url ?? '' : $this->name ?? '';
	}

	final public function jsonSerialize(): array
	{
		return [
			'name'     => $this->name,
			'size'     => $this->size,
			'type'     => $this->type,
			'error'    => $this->error,
			'saved'    => $this->saved,
			'location' => $this->location,
			'hash'     => $this->hash,
			'ext'      => $this->ext,
			'url'      => $this->url,
		];
	}

	final public function hasError(): bool
	{
		return isset($this->error);
	}

	final public function valid(): bool
	{
		return ! $this->hasError();
	}

	final private function _parse(array $file)
	{
		$name = empty($file['name']) ? 'File' : sprintf('"%s"', $file['name']);
		if ($file['error'] !== UPLOAD_ERR_OK) {
			switch ($file['error']) {
				case  UPLOAD_ERR_INI_SIZE:
				case UPLOAD_ERR_FORM_SIZE:
					$this->_error = new HTTPException("{$name} exceeds maximum size of {$this->maxUploadSize}", HTTP::PAYLOAD_TOO_LARGE);
					break;
				case UPLOAD_ERR_PARTIAL:
					$this->_error = new HTTPException("{$name} partially uploaded", HTTP::BAD_REQUEST);
					break;
				case UPLOAD_ERR_NO_FILE:
					$this->_error = new HTTPException('No file uploaded', HTTP::BAD_REQUEST);
					break;
				case UPLOAD_ERR_NO_TMP_DIR:
					$this->_error = new HTTPException('No temporary directory for uploads', HTTP::INTERNAL_SERVER_ERROR);
					break;
				case UPLOAD_ERR_CANT_WRITE:
					$this->_error = new HTTPException('Cannot write to tmp dir', HTTP::INTERNAL_SERVER_ERROR);
					break;
				case UPLOAD_ERR_EXTENSION:
					$this->_error = new HTTPException("An extension blocked upload of {$name}", HTTP::INTERNAL_SERVER_ERROR);
					break;
				default:
					$this->_error = new HTTPException("An unknown error occured uploading {$name}", HTTP::INTERNAL_SERVER_ERROR);
			}
		} else {
			$this->_name     = $file['name'];
			$this->_tmp_name = $file['tmp_name'];
			$this->_size     = $file['size'];
			$this->_type     = $file['type'];
			$this->_location = $file['tmp_name'];
			$this->_setFilePath($file['tmp_name']);

			if ($this->_type === '') {
				$this->_type = mime_content_type($file['tmp_name']);
			}
		}
	}
}
