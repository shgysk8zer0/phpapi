<?php
namespace shgysk8zer0\PHPAPI\Traits;
use \Exception;
use \InvalidArgumentException;
use \StdClass;
use \CURLFile;
use \shgysk8zer0\PHPAPI\{URL};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};

trait cURL
{
	private $_url     = null;
	private $_headers = [];
	private $_method  = 'GET';
	private $_body    = [];

	final public function setURL(string $url): void
	{
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			if (strpos($url, '?') !== false) {
				list ($url, $query) = explode('?', $url, 2);
				parse_str($query, $data);
				$this->_body = $data;
			}
			$this->_url = $url;
		} else {
			throw new InvalidArgumentException("{$url} is not a valid URL");
		}
	}

	final public function setHeader(string $key, string $value): void
	{
		$this->_headers[$key] = $value;
	}

	final public function setHeaders(array $headers): void
	{
		array_map([$this, 'setHeader'], array_keys($headers), array_values($headers));
	}

	final public function setMethod(string $method): void
	{
		$this->_method = $method;
	}

	final public function setParam(string $key, $value): void
	{
		$this->_body[$key] = $value;
	}

	final public function setParams(array $params): void
	{
		array_map([$this, 'setParam'], array_keys($params), array_values($params));
	}

	final public function addFile(string $name, string $filename): void
	{
		if (file_exists($filename)) {
			$this->setParam($name, new CURLFile($filename, mime_content_type($filename), $name));
		} else {
			throw new Exception("{$filename} not found");
		}
	}

	final protected function _send(bool $assoc = false): ?object
	{
		if (isset($this->_url)) {
			$ch = curl_init();
			curl_setopt_array($ch, [
				CURLOPT_URL            => $this->_url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_HTTPHEADER     => $this->_getHeaders(),
				CURLOPT_FOLLOWLOCATION => true,
			]);
			switch($this->_method) {
				case 'GET':
					if (! empty($this->_body)) {
						curl_setopt(
							$ch,
							CURLOPT_URL,
							sprintf('%s?%s', $this->_url, http_build_query($this->_body))
						);
					}
					break;
				case 'POST':
					curl_setopt($ch, CURLOPT_POST, true);
					if (array_key_exists('Content-Type', $this->_headers)) {
						switch($this->_headers['Content-Type']) {
							case 'application/json':
								curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->_body));
								break;
							default:
								curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_body);
						}
					} else {
						curl_setopt($ch, CURLOPT_POSTFIELDS, $this->_body);
					}
					break;
				case 'PUT':
					curl_setopt($ch, CURLOPT_PUT, true);
					break;
				default:
					throw new Exception("Unsupported method: {$this->_method}");
			}
			$result = curl_exec($ch);
			$resp = new StdClass();
			$resp->body = null;
			$resp->headers = new StdClass();

			if ($result !== false) {
				$resp->url = new URL(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
				$resp->status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				$resp->headers->{'Content-Type'} = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
				$resp->headers->{'Content-Length'} = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
				$resp->ok = $resp->status >= 200 && $resp->status <= 299;

				switch (strtolower($resp->headers->{'Content-Type'})) {
					case 'application/json':
						$data = json_decode($result, $assoc);
						$resp->body = $data;
						break;
					case 'text/plain':
					default:
						$resp->body = $result;
				}
				$resp->error = null;
			} else {
				$resp->status = HTTP::BAD_GATEWAY;
				$resp->ok = false;
				$resp->body = null;
				$resp->error = new Exception(curl_error($ch), curl_errno($ch));
			}
			curl_close($ch);
			return $resp;
		} else {
			throw new Exception('Missing required URL for cURL request');
		}
	}

	final private function _getHeaders(): array
	{
		return array_map(function(string $key, string $value): string
		{
			return sprintf('%s: %s', $key, $value);
		}, array_keys($this->_headers), array_values($this->_headers));
	}
}
