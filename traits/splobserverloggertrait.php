<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Interfaces\{SPLSubjectLoggerInterface};
use \InvalidArgumentException;
use \SPLSubject;

trait SPLObserverLoggerTrait
{
	final public function update(SPLSubject $subject): void
	{
		if ($subject instanceof SPLSubjectLoggerInterface) {
			$this->log($subject->getLevel(), $subject->getMessage(), $subject->getContext());
		} else {
			throw new InvalidArgumentException(sprintf(
				'%s must implement SPLSubjectLoggerInterface',
				get_class($subject)
			));
		}
	}
}
