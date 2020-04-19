<?php

namespace shgysk8zer0\PHPAPI\WebHook;

use \shgysk8zer0\PHPAPI\{HTTPException, NullLogger};
use \shgysk8zer0\PHPAPI\Traits\{Git, LoggerAwareTrait};
use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface, LoggerInterface};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPStatusCodes as HTTP};
use \JsonSerializable;
use \Throwable;

final class GitHub implements JsonSerializable, LoggerAwareInterface
{
	use Git;
	use LoggerAwareTrait;

	private const _HOOKSHOT    = '/^GitHub-Hookshot/';
	private const _MASTER      = 'refs/heads/master';

	private $_config  = null;
	private $_data    = [];
	private $_payload = null;
	private $_event   = null;
	private $_headers = null;
	private $_id      = null;

	public function __construct(string $config, LoggerInterface $logger = null)
	{
		if (isset($logger)) {
			$this->setLogger($logger);
		} else {
			$this->setLogger(new NullLogger());
		}

		$this->_getHeaders();

		try {
			if (! array_key_exists('x-github-event', $this->_headers)) {
				throw new HTTPException('Missing X-GitHub-Event header', HTTP::BAD_REQUEST);
			} elseif (! array_key_exists('x-github-delivery', $this->_headers)) {
				throw new HTTPException('Missing X-GitHub-Delivery header', HTTP::BAD_REQUEST);
			} elseif (! file_exists($config)) {
				throw new HTTPException('Config file not found', HTTP::INTERNAL_SERVER_ERROR);
			} else {
				$this->_event  = $this->_headers['x-github-event'];
				$this->_id     = $this->_headers['x-github-delivery'];
				$this->_config = json_decode(file_get_contents($config));
				$this->_parse();
			}
		} catch (Throwable $e) {
			$this->_logException($e);
			throw $e;
		}
	}

	public function __debugInfo(): array
	{
		return [
			'config'   => $this->_config,
			'data'     => $this->_data,
			'payload'  => $this->_payload,
			'headers'  => $this->_headers,
			'event'    => $this->_event,
			'branch'   => $this->getBranch(),
			'clean'    => $this->isClean(),
			'status'   => explode($this->status()),
		];
	}

	public function __get(string $key)
	{
		switch($key) {
			case 'event': return $this->_event;
			case 'id': return $this->_id;
			case 'length': return (isset($this->length)) ? intval($this->_headers['content-length']) : 0;
			case 'contentType': return $this->_headers['content-type'];
			case 'signature': return $this->_headers['x-hub-signature'];
			default: return $this->_data->{$key};
		}
	}

	public function __isset(string $key): bool
	{
		switch($key) {
			case 'event': return is_string($this->_event);
			case 'id': return is_string($this->_id);
			case 'length': return array_key_exists('content-length', $this->_headers);
			case 'contentType': return array_key_exists('content-type', $this->_headers);
			case 'signature': return array_key_exists('x-hub-signature', $this->_headers);
			default: return isset($this->_data->{$key});
		}
	}

	public function jsonSerialize(): array
	{
		return [
			'data'     => $this->_data,
			'event'    => $this->_event,
			'branch'   => $this->getBranch(),
			'clean'    => $this->isClean(),
			'status'   => explode(PHP_EOL, $this->status()),
		];
	}
	public function isMaster(): bool
	{
		return isset($this->ref) && $this->ref === self::_MASTER;
	}

	private function  _getHeaders(): bool
	{
		if (is_null($this->_headers)) {
			$headers = getallheaders();
			$keys    = array_map('strtolower', array_keys($headers));
			$values  = array_values($headers);

			$this->_headers = array_combine($keys, $values);
		}
		return is_array($this->_headers);
	}

	private function _parse()
	{
		$type = strtolower($this->contentType);
		$type = preg_replace('/;\s?boundary=[A-z\d]+$/', null, $type);

		switch($type) {
		case 'multipart/form-data':
		case 'x-www-form-url-encoded':
			throw new HTTPException('Form data currently not supported', HTTP::NOT_IMPLEMENTED);
		case 'application/json':
			if (isset($this->length)) {
				$payload = file_get_contents('php://input');
				$length  = strlen($payload);
				$data    = json_decode($payload);

				if ($length !== $this->length) {
					throw new HTTPException('Content-Length does not match payload size', HTTP::BAD_REQUEST);
				} elseif (isset($this->_config->secret)) {
					if (array_key_exists('x-hub-signature', $this->_headers)) {
						if ($this->_verifySecret($this->_headers['x-hub-signature'], $payload, $this->_config->secret)) {
							$this->_payload = $payload;
							$this->_data = $data;
						} else {
							throw new HTTPException('Invalid Signature', HTTP::BAD_REQUEST);
						}
					} else {
						throw new HTTPException('Missing Signature', HTTP::BAD_REQUEST);
					}
				} else {
					$this->_payload = $payload;
					$this->_data = $data;
				}
			} else {
				throw new HTTPException('Content-Length header required', HTTP::LENGTH_REQUIRED);
			}
			break;
		default:
			throw new HTTPException(sprintf('Unsupported Content-Type: %s', $type), HTTP::UNSUPPORTED_MEDIA_TYPE);
		}
	}

	private function _logException(Throwable $e): void
	{
		$this->logger->error('[{class} {code}] "{message}" at {file}:{line}', [
			'class'   => get_class($e),
			'code'    => $e->getcode(),
			'message' => $e->getMessage(),
			'file'    => $e->getFile(),
			'line'    => $e->getLine(),
		]);
	}

	private function _verifySecret(string $sig, string $payload, string $secret): bool
	{
		list($algo, $hmac) = explode('=', $sig, 2) + [null, null];

		if (is_null($algo) or is_null($hmac)) {
				return false;
		} elseif (! in_array($algo, hash_algos(), true)) {
			return false;
		} elseif (! hash_equals(hash_hmac($algo, $payload, $secret), $hmac)) {
			$this->logger->warning('HMAC verification failed for GitHub Webhook');
			return false;
		} else {
			return true;
		}
	}
}
