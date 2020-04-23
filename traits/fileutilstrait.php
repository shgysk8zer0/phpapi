<?php
namespace shgysk8zer0\PHPAPI\Traits;
use \InvalidArgumentException;
trait FileUtilsTrait
{
	protected $_handle = null;

	public function open(string $fname, string $mode = 'r'): bool
	{
		if ($this->isOpen()) {
			$this->close();
		}

		$this->_handle = fopen($fname, $mode);
		return $this->isOpen();
	}

	final public function close(): bool
	{
		if ($this->isOpen()) {
			$this->unlock();
			fclose($this->_handle);
			$this->_handle = null;
			return true;
		} else {
			return false;
		}
	}

	final public function copyTo($resource, bool $rewind = true): bool
	{
		if (! is_resource($resource)) {
			throw new InvalidArgumentException(sprintf('Expected a resource but got a %s', get_type($resource)));
		}
		if ($this->isOpen()) {
			if ($rewind) {
				$this->rewind();
			}

			return stream_copy_to_stream($this->_handle, $resource) !== false;
		} else {
			return false;
		}
	}

	final public function isOpen(): bool
	{
		return is_resource($this->_handle);
	}

	final public function isEnd(): bool
	{
		return $this->isOpen() and feof($this->_handle);
	}

	final public function getPosition():? int
	{
		if ($this->isOpen()) {
			return ftell($this->_handle);
		} else {
			return null;
		}
	}

	final public function rewind(): bool
	{
		return $this->isOpen() and rewind($this->_handle);
	}

	final public function seek(int $offset, int $whence = SEEK_SET): bool
	{
		return $this->isOpen() and fseek($this->_handle, $offset, $whence) === 0;
	}

	final public function truncate(int $size = 0): bool
	{
		return $this->isOpen() and ftruncate($this->_handle, $size);
	}

	final public function read():? string
	{
		if ($this->isOpen()) {
			return fgets($this->_handle);
		} else {
			return null;
		}
	}

	final public function write(string $text):? int
	{
		if ($this->isOpen()) {
			return fwrite($this->_handle, $text);
		} else {
			return null;
		}
	}

	final public function lock(int $mode = LOCK_SH): bool
	{
		return $this->isOpen() and flock($this->_handle, $mode);
	}

	final public function unlock(): bool
	{
		return $this->lock(LOCK_UN);
	}
}
