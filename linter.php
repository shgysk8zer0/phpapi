<?php

namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\{NullLogger};
use \shgysk8zer0\PHPAPI\Traits\{LoggerAwareTrait};
use \shgysk8zer0\PHPAPI\Interfaces\{LoggerAwareInterface, LoggerInterface};
use \FilesystemIterator as FS;
use \InvalidArgumentException;
use \RuntimeException;
use \Throwable;

final class Linter implements LoggerAwareInterface
{
	use LoggerAwareTrait;

	private $_scan_exts    = ['php', 'phtml'];

	private $_ignored_dirs = [];

	public function __construct(?LoggerInterface $logger = null)
	{
		if (! function_exists('php_check_syntax')) {
			throw new RuntimeException('`php_check_syntax` function not available');
		} elseif (isset($logger)) {
			$this->setLogger($logger);
		} else {
			$this->setLogger(new NullLogger());
		}
	}

	final public function ignoreDirs(string ...$dirs): void
	{
		$dirs = array_filter($dirs, 'is_dir');
		$this->_ignored_dirs = array_map(function(string $path): string
		{
			return realpath($path);
		}, $dirs);
	}

	final public function scanExts(string ...$exts): void
	{
		$this->_scan_exts = array_map(function(string $ext): string
		{
			return strtolower($ext);
		}, $exts);
	}

	final public function isAllowedDir(FS $path): bool
	{
		// $this->logger->notice('Checking {path}', ['path' => $path->getPathName()]);
		return $path->isDir() and ! in_array(realpath($path->getPathname()), $this->_ignored_dirs);
	}

	final public function isLintable(FS $path): bool
	{
		return $path->isFile() and in_array($path->getExtension(), $this->_scan_exts);
	}

	final public function scan(string $path): bool
	{
		if (is_dir($path)) {
			$dir = new FS($path, FS::KEY_AS_PATHNAME | FS::CURRENT_AS_SELF | FS::SKIP_DOTS);
			$valid = true;

			foreach ($dir as $path => $fs) {
				if ($this->isAllowedDir($fs)) {
					$valid = $this->scan($path) && $valid;
				} elseif ($this->isLintable($fs)) {
					$valid = $this->lintFile($fs) && $valid;
				} elseif ($fs->isDir()) {
					$this->logger->info('Ignoring {path}', ['path' => $path]);
				}
			}

			return $valid;
		} else {
			throw new InvalidArgumentException(sprintf('%s is not a directory', $path));
		}
	}

	final public function lintFile(FS $path): bool
	{
		$valid = true;

		if ($this->isLintable($path)) {
			$this->logger->info('Linting file: {file}', ['file' => $path]);
			$msg = '';
			try {
				if (! @php_check_syntax($path->getPathname(), $msg)) {
					$valid = false;
					$this->logger->error('Error in {file} with message {msg}', [
						'file' => $path,
						'msg'  => $msg,
					]);
				}
			} catch (Throwable $e) {
				$valid = false;
				$this->logger->error('[{type} {code}] "{message}" at {file}:{line}', [
					'type'    => get_class($e),
					'code'    => $e->getCode(),
					'message' => $e->getMessage(),
					'file'    => $e->getFile(),
					'line'    => $e->getLine(),
				]);
			} finally {
				return $valid;
			}
		} else {
			throw new InvalidArgumentException(sprintf('Cannot lint path: %s', $path->getPathne()));
		}
	}
}
