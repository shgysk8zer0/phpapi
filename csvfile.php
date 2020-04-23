<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerAwareTrait};
use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};
use \InvalidArgumentException;
use \Throwable;
use \RuntimeException;

class CSVFile implements LoggerAwareInterface
{
	use LoggerAwareTrait;

	private $_handle = null;

	public function __construct()
	{
		$this->setLogger(new NullLogger());
	}

	public function __destruct()
	{
		$this->close();
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
		$this->close();

		try {
			if (! $this->_handle = fopen($fname, $mode)) {
				throw new RuntimeException("Unable to open {$fname}");
			} elseif (! flock($this->_handle, LOCK_SH)) {
				throw new RuntimeException("Unable to acquire lock for {$fname}");
			} else {
				return true;
			}
		} catch (Throwable $e) {
			$this->_logException($e);

			return false;
		}
	}

	public function close(): bool
	{
		if (is_resource($this->_handle)) {
			flock($this->_handle, LOCK_UN);
			fclose($this->_handle);
			$this->_handle = null;
			return true;
		} else {
			return false;
		}
	}

	public function write(array $row): bool
	{
		if (is_resource($this->_handle)) {
			return fputcsv($this->_handle, $row);
		} else {
			return false;
		}
	}

	public function rows(bool $with_header = false): iterable
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
			return [];
		}
	}

	private function _logException(Throwable $e, string $level = LogLevel::ERROR): void
	{
		$this->logger->log($level, '[{type} {code}] "{message}" at {file}:{line}', [
			'type'    => get_class($e),
			'code'    => $e->getCode(),
			'message' => $e->getMessage(),
			'file'    => $e->getFile(),
			'line'    => $e->getLine(),
		]);
	}
}
