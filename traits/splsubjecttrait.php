<?php
namespace shgysk8zer0\PHPAPI\Traits;

use \SplObserver;

trait SPLSubjectTrait
{
	private $_observers = [];

	final public function attach(SplObserver $observer): void
	{
		if (! in_array($observer, $this->_observers)) {
			$this->_observers[] = $observer;
		}
	}

	final public function detach(SplObserver $observer): void
	{
		if ($index = array_search($observer, $this->_observers, true)) {
			unset($this->_observers[$observer]);
		}
	}

	final protected function _getSPLObservers(): array
	{
		return $this->_observers;
	}

	abstract public function notify(): void;
}
