<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{FileUtilsInterface};
use \shgysk8zer0\PHPAPI\Traits\{FileUtilsTrait};
use \RuntimeException;

class TmpFile implements FileUtilsInterface
{
	use FileUtilsTrait;

	public function __construct()
	{
		$this->open();
		$this->lock(LOCK_EX);
	}

	public function __destruct()
	{
		$this->unlock();
		$this->close();
	}

	public function open(): bool
	{
		if ($this->isOpen()) {
			$this->close();
		}
		$this->_handle = tmpfile();
		return $this->isOpen();
	}

	public function saveAs(string $fname): bool
	{
		try {
			if (! $this->isOpen()) {
				throw new RuntimeException('Cannot save from an unopened file');
			}
			$handle = fopen($fname, 'w');
			flock($handle, LOCK_EX);
			$pos = $this->getPosition();
			$this->rewind();
			$this->copyTo($handle);
			flock($handle, LOCK_UN);
			fclose($handle);
			$this->seek($pos);
			return true;
		} catch (Throwable $e) {
			return false;
		}
	}
}
