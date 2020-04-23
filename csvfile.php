<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerAwareTrait, FileUtilsTrait};
use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};
use \InvalidArgumentException;
use \Throwable;
use \RuntimeException;

class CSVFile implements LoggerAwareInterface
{
	use LoggerAwareTrait;
	use FileUtilsTrait;

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
		if ($this->isOpen()) {
			$this->rewind();
			$text = '';

			while ($line = $this->read()) {
				$text .= $line;
			}

			return $text;
		} else {
			return '';
		}
	}

	public function writeRow(array $row): bool
	{
		if ($this->isOpen()) {
			try {
				return fputcsv($this->_handle, $row);
			} catch (Throwable $e) {
				$this->_logException($e);
				return false;
			}
		} else {
			return false;
		}
	}

	public function rows(bool $with_header = false): iterable
	{
		if ($this->isOpen()) {
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
