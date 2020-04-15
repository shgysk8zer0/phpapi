<?php
namespace shgysk8zer0\PHPAPI;
use \JsonSerializable;
use \Iterator;

use \shgysk8zer0\PHPAPI\Interfaces\{
	InputData as InputDataInterface,
	LoggerAwareInterface,
};

use \shgysk8zer0\PHPAPI\Traits\{
	Singleton,
	InputData as InputDataTrait,
	LoggerAwareTrait,
};

class PostData implements JSONSerializable, Iterator, InputDataInterface, LoggerAwareInterface
{
	use Singleton;
	use InputDataTrait;
	use LoggerAwareTrait;

	final public function __construct(array $data = null)
	{
		$this->setLogger(new NullLogger());
		if (isset($data)) {
			$this->_setInputData($data);
		} elseif (array_key_exists('CONTENT_TYPE', $_SERVER)) {
			switch (strtolower($_SERVER['CONTENT_TYPE'])) {
				case 'application/json':
				case 'text/json':
					$this->_setInputData(json_decode(file_get_contents('php://input'), true));
					break;
				case 'application/csp-report':
					$report = json_decode(file_get_contents('php://input'), true);
					if (array_key_exists('csp-report', $report)) {
						$this->_setInputData($report['csp-report']);
					}
					break;
				case 'text/plain':
				case 'application/text':
					$this->_setInputData(['text' => file_get_contents('php://input')]);
					break;
				default:
					$this->_setInputData($_POST);
			}
		} else {
			$this->_setInputData($_POST);
		}
	}
}
