<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\Traits\{cURL};

class Request implements \JSONSerializable
{
	use cURL;

	public function __construct(
		string $url,
		string $method  = 'GET',
		array  $headers = [],
		array  $params  = []
	)
	{
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

	final public function send(): \StdClass
	{
		return $this->_send();
	}
}
