<?php
namespace shgysk8zer0\PHPAPI\Interfaces;
use \InvalidArgumentException;
interface FileUtilsInterface
{
	public function open(string $fname, string $mode = 'r'): bool;

	public function close(): bool;

	public function copyTo($resource, bool $rewind = true): bool;

	public function isOpen(): bool;

	public function isEnd(): bool;

	public function getPosition():? int;

	public function rewind(): bool;

	public function seek(int $offset, int $whence = SEEK_SET): bool;

	public function truncate(int $size = 0): bool;

	public function read():? string;

	public function write(string $text):? int;

	public function lock(int $mode = LOCK_SH): bool;

	public function unlock(): bool;
}
