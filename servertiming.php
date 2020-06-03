<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface, LoggerInterface};
use \shgysk8zer0\PHPAPI\Traits\{LoggerAwareTrait};

final class ServerTiming implements LoggerAwareInterface
{
	use LoggerAwareTrait;

	private const HEADER = 'Server-Timing';

	private $_origins = [];

	private $_name = null;

	private $_desc = null;

	private $_start = null;

	private $_end = null;

	public function __construct(string $name, ?string $desc = null, ?LoggerInterface $logger = null)
	{
		if (isset($logger)) {
			$this->setLogger($logger);
			$this->logger->debug('New timer: {name}', ['name' => $name]);
		} else {
			$this->setLogger(new SAPILogger());
		}

		$this->setName($name);
		$this->setDescription($desc);
	}

	public function getDescription(bool $escape = false):? string
	{
		if ($escape and isset($this->_desc)) {
			return urlencode($this->_desc);
		} else {
			return $this->_desc;
		}
	}

	public function setDescription(?string $val): void
	{
		$this->_desc = $val;
	}

	public function getDuration():? float
	{
		if (isset($this->_start, $this->_end)) {
			return $this->_end - $this->_start;
		} else {
			return null;
		}
	}

	final public function allowOrigins(string ...$origins): bool
	{
		$valid = true;

		foreach ($origins as $origin) {
			if (filter_var($origin, FILTER_VALIDATE_URL)) {
				['scheme' => $scheme, 'host' => $host, 'port' => $port]
					= array_merge(['schema' => 'http', 'host' => null, 'port' => null], parse_url($origin));
				if (isset($port)) {
					$this->_origins[] = strtr('{scheme}://{host}:{port}', [
						'{scheme}' => $scheme,
						'{host}'   => $host,
						'{port}'   => $port,
					]);
				} else {
					$this->_origins[] = strtr('{scheme}://{host}', [
						'{scheme}' => $scheme,
						'{host}'   => $host,
						'{port}'   => $port,
					]);
				}
			} else {
				$this->logger->error('Invalid origin: {origin}', ['origin' => $origin]);
				$valid = false;
				$this->_origins = [];
				break;
			}
		}

		return $valid;
	}

	final public function allowedOrigin(): bool
	{
		if (! array_key_exists('HTTP_ORIGIN', $_SERVER)) {
			return true;
		} else {
			return in_array($_SERVER['HTTP_ORIGIN'], $this->getOrigins());
		}
	}

	final public function getOrigins():? array
	{
		if (count($this->_origins) === 0) {
			return null;
		} else {
			return $this->_origins;
		}
	}

	final public function sameOrigin(): bool
	{
		if (! in_array('HTTP_HOST', $_SERVER)) {
			return false;
		} elseif ($_SERVER['HTTP_HOST'] === strtr('{scheme}://{host}{port}', [
			'{scheme}' => array_key_exists('HTTPS', $_SERVER) and ! empty($_SERVER['HTTPS']) ? 'https' : 'http',
			'{host}'   => $_SERVER['SERVER_NAME'] ?? 'localhost',
			'{port}'   => array_key_exists('SERVER_PORT', $_SERVER) and ! in_array($_SERVER['SERVER_PORT'], [80, 443]) ? ":{$_SERVER['SERVER_PORT']}" : null,
		])) {
			return true;
		} else {
			return false;
		}
	}

	public function getName(bool $escape = false):? string
	{
		if ($escape) {
			return urlencode($this->_name);
		} else {
			return $this->_name;
		}
	}

	public function setName(string $val): void
	{
		$this->_name = $val;
	}

	public function start(): bool
	{
		$this->logger->debug('Starting {name}', ['name' => $this->getName()]);
		if (! $this->running()) {
			$this->_start = microtime(true);
			return true;
		} else {
			return false;
		}
	}

	public function stop(): bool
	{
		$this->logger->debug('Stopping {name}', ['name' => $this->getName()]);
		if ($this->running()) {
			$this->_end = microtime(true);
			return true;
		} else {
			return false;
		}
	}

	public function running(): bool
	{
		return isset($this->_start) and is_null($this->_end);
	}

	public function send(bool $replace = false): bool
	{
		$dur = $this->getDuration();

		if (! function_exists('header')) {
			$this->logger->error('header() function not available');
		} elseif (headers_sent()) {
			$this->logger->warning('Headers already sent for {name}', ['name' => $this->getName()]);
			return false;
		} elseif (is_null($dur)) {
			return false;
		} elseif ($this->sameOrigin() or $this->allowedOrigin()) {
			if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
				if (empty($this->_origins)) {
					header('Timing-Allow-Origin: *');
				} else {
					header(sprintf('Timing-Allow-Origin: %s', join(', ', $this->_origins)));
				}
			}

			$name = $this->getName();
			$desc = $this->getDescription();

			if ($this->running()) {
				$this->logger->debug('Stopping timer for {name}', ['name' => $this->getName()]);
				$this->stop();
			}

			$this->logger->debug('sending: name: {name}, desc: {desc}, dur: {dur}', [
				'name' => $this->getName(),
				'desc' => $this->getDescription() ?? 'unset',
				'dur'  => $dur,
			]);

			if (isset($desc)) {
				header(sprintf('%s: %s;desc="%s";dur=%s', self::HEADER, $name, $desc, round($dur, 2)), $replace);
			} else {
				header(sprintf('%s: %s;dur=%s', self::HEADER, $name, round($dur, 2)), $replace);
			}

			return true;
		} else {
			$this->logger->warning('Timer for {name} not started or still running [started: {started}, stopped: {stopped}', [
				'name' => $this->getName(),
				'started' => isset($this->_start) ? 'Yes': 'No',
				'stopped' => isset($this->_end) ? 'Yes' : 'No',
			]);
			return false;
		}
	}
}
