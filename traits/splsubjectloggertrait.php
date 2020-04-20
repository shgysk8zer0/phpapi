<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Abstracts\{LogLevel};

trait SplSubjectLoggerTrait
{
	private $_level = LogLevel::DEBUG;

	private $_message = '';

	private $_context = [];

	final public function getLevel(): string
	{
		return $this->_level;
	}

	final public function setLevel(string $level): void
	{
		$this->_level = $level;
	}

	final public function getMessage(): string
	{
		return $this->_message;
	}

	final public function setMessage(string $message): void
	{
		$this->_message = $message;
	}

	final public function getContext(): array
	{
		return $this->_context;
	}

	final public function setContext(array $context): void
	{
		$this->_context = $context;
	}
}
