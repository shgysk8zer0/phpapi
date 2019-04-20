<?php
namespace shgysk8zer0\PHPAPI;

class PostData implements \JSONSerializable, \Iterator
{
	use Traits\Singleton;
	use Traits\InputData;

	final public function __construct()
	{
		if (array_key_exists('CONTENT_TYPE', $_SERVER)) {
			switch (strtolower($_SERVER['CONTENT_TYPE'])) {
				case 'application/json':
				case 'text/json':
					static::_setInputData(json_decode(file_get_contents('php://input'), true));
					break;
				case 'application/csp-report':
					$report = json_decode(file_get_contents('php://input'), true);
					if (array_key_exists('csp-report', $report)) {
						static::_setInputData($report['csp-report']);
					}
					break;
				case 'text/plain':
				case 'text/ping':
				case 'application/text':
					static::_setInputData(['text' => file_get_contents('php://input')]);
					break;
				default:
					static::_setInputData($_POST);
			}
		} else {
			static::_setInputData($_POST);
		}
	}
}
