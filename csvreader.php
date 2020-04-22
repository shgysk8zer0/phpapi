<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerAwareTrait};
use \InvalidArgumentException;
use \Throwable;
use \RuntimeException;

class CSVReader implements LoggerAwareInterface
{
	use LoggerAwareTrait;

	private $_handle = null;
	private $_filename = null;

	public function __construct()
	{
		$this->setLogger(new NullLogger());
	}

	public function __destruct()
	{
		if (is_resource($this->_handle)) {
			flock($this->_handle, LOCK_UN);
			fclose($this->_handle);
			$this->_handle = null;
		}
	}

	public function __toString(): string
	{
		if (is_resource($this->_handle)) {
			rewind($this->_handle);
			$text = '';

			while ($line = fgets($this->_handle)) {
				$text .= $line;
			}

			return $text;
		} else {
			return '';
		}
	}

	public function open(string $fname, string $mode = 'r'): bool
	{
		try {
			if (! file_exists($fname)) {
				throw new InvalidArgumentException("{$fname} not found");
			} elseif (strtolower(pathinfo($fname, PATHINFO_EXTENSION)) !== 'csv') {
				throw new InvalidArgumentException("{$fname} does not appear to be a CSV file");
			} elseif (! $this->_handle = fopen($fname, $mode)) {
				throw new RuntimeException("Unable to open {$fname}");
			} elseif (! flock($this->_handle, LOCK_SH)) {
				throw new RuntimeException("Unable to acquire lock for {$fname}");
			} else {
				return true;
			}
		} catch (Throwable $e) {
			$this->logger->error('[{type} {code}] "{message}" at {file}:{line}', [
				'type'    => get_class($e),
				'code'    => $e->getCode(),
				'message' => $e->getMessage(),
				'file'    => $e->getFile(),
				'line'    => $e->getLine(),
			]);

			return false;
		}
	}

	public function rows(bool $with_header = false):? iterable
	{
		if (is_resource($this->_handle)) {
			rewind($this->_handle);

			if ($with_header) {
				$headers = fgetcsv($this->_handle);
				$len = count($headers);

				while ($row = fgetcsv($this->_handle)) {
					yield array_combine($headers, (array_pad($row, $len, null)));
				}
			} else {
				while ($row = fgetcsv($this->_handle)) {
					yield $row;
				}
			}
		} else {
			return null;
		}
	}
}
