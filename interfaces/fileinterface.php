<?php
namespace shgysk8zer0\Interfaces;

interface FileInterface
{
	public function open(string $fname): bool;

	public function write(string $content): bool;

	public function valid(): bool;

	public function getType(): string;
}
