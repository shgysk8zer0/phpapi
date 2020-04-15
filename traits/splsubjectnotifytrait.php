<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait SPLSubjectNotifyTrait
{
	final public function notify(): void
	{
		foreach ($this->_getSPLObservers() as $observer) {
			$observer->update($this);
		}
	}

	abstract protected function _getSPLObservers(): array;
}
