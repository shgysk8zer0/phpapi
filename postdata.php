<?php
namespace shgysk8zer0\PHPAPI;

class PostData implements \JSONSerializable, \Iterator, Interfaces\InputData
{
	use Traits\Singleton;
	use Traits\InputData;

	final public function __construct(array $data = null)
	{
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
