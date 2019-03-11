<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{Headers};

class HTTPException extends \Exception implements \JSONSerializable
{
	final public function __construct(string $message, int $code = Headers::INTERNAL_SERVER_ERROR, \Throwable $prev = null)
	{
		parent::__construct($message, $code, $prev);
	}

	final public function __toString(): string
	{
		return $this->getMessage();
	}

	final public function jsonSerialize(): array
	{
		return [
			'error' => [
				'message' => $this->getMessage(),
				'code'    => $this->getCode(),
			]
		];
	}

	final public function __invoke(bool $exit = true): void
	{
		Headers::status($this->getCode());
		Headers::set('Content-Type', 'application/json');
		echo json_encode($this);
		if ($exit) {
			exit();
		}
	}
}
