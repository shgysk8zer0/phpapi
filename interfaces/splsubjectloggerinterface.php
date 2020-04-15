<?php
namespace shgysk8zer0\PHPAPI\Interfaces;
use \SPLSubject;
use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};

interface SPLSubjectLoggerInterface extends SPLSubject
{
	public function getLevel(): string;

	public function getMessage(): string;

	public function getContext(): array;
}
