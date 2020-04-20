<?php
namespace shgysk8zer0\PHPAPI\Interfaces;

use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};
use \SPLSubject;

interface SPLSubjectLoggerInterface extends SPLSubject
{
	public function getLevel(): string;

	public function setLevel(string $val): void;

	public function getMessage(): string;

	public function setMessage(string $val): void;

	public function getContext(): array;

	public function setContext(array $val): void;
}
