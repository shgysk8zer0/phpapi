<?php

namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Abstracts\LogLevel;

use \InvalidArgumentException;

trait LoggerLevelsTrait
{
	private $_levels = LogLevel::ALL_LEVELS;

	final public function allowsLevel(string $level): bool
	{
		return in_array($level, $this->_levels);
	}

	final public function enableLevel(string $level): bool
	{
		if ($this->validLevel($level) and ! $this->allowsLevel($level)) {
			$this->_levels[] = $level;
			return true;
		} else {
			return false;
		}
	}

	final public function disableLevel(string $level): bool
	{
		if ($this->validLevel($level) and $this->allowsLevel($level)) {
			unset($this->_levels[array_search($level, $this->_levels)]);
			return true;
		} else {
			return false;
		}
	}

	final public function enableLevels(string ...$levels): void
	{
		foreach ($levels as $level) {
			$this->enableLevel($level);
		}
	}

	final public function disableLevels(string ...$levels): void
	{
		foreach ($levels as $level) {
			$this->disableLevel($level);
		}
	}

	final public function enableAllLevels(): void
	{
		$this->_levels = LogLevel::ALL_LEVELS;
	}

	final public function disableAllLevels(): void
	{
		$this->_levels = [];
	}

	final public function validLevel(string $level): bool
	{
		return in_array($level, LogLevel::ALL_LEVELS);
	}
}
