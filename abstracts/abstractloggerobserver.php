<?php
namespace shgysk8zer0\PHPAPI\Abstracts;

use \shgysk8zer0\PHPAPI\Interfaces\{LoggerInterface, SPLSubjectLoggerInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerTrait};
use \SPLObserver;
use \SplSubject;
use \InvalidArgumentException;

abstract class AbstractLoggerObserver implements LoggerInterface, SplObserver
{
	use LoggerTrait;

	final public function update(SplSubject $subject): void
	{
		if ($subject instanceof SPLSubjectLoggerInterface) {
			$this->log($subject->getLevel(), $subject->getMessage(), $subject->getContext());
		} else {
			throw new InvalidArgumentException('Subject does not implmenet SPLSubjectLoggerInterface');
		}
	}

	abstract public function log(string $level, string $message, array $context = []): void;
}
