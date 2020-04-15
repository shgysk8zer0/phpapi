<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\Traits\{cURL, LoggerAwareTrait};
use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface};
use \JsonSerializable;

class Request implements JSONSerializable, LoggerAwareInterface
{
	use cURL;
	use LoggerAwareInterface;

	public function __construct(
		string $url,
		string $method  = 'GET',
		array  $headers = [],
		array  $params  = []
	)
	{
		$this->setLogger(new NullLogger());
		$this->setURL($url);
		$this->setMethod($method);
		$this->setHeaders($headers);
		$this->setParams($params);
	}

	public function jsonSerialize(): array
	{
		return [
			'url'     => $this->_url,
			'method'  => $this->_method,
			'headers' => $this->_headers,
			'body'    => $this->_body,
		];
	}

	final public function send(bool $assoc = false): object
	{
		return $this->_send($assoc);
	}
}
