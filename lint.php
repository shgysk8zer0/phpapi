<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{Linter};
use \LogicException;
const BASE = __DIR__ . DIRECTORY_SEPARATOR;

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit();
} elseif (! isset($argv) or ! is_array($argv) or realpath($argv[0])!== __FILE__) {
	throw new LogicException('Linter must be called directly');
} else {
	// Load required files
	require_once BASE . 'abstracts/loglevel.php';
	require_once BASE . 'traits/loggerawaretrait.php';
	require_once BASE . 'interfaces/loggerawareinterface.php';
	require_once BASE . 'interfaces/loggerinterface.php';
	require_once BASE . 'traits/loggertrait.php';
	require_once BASE . 'traits/splobserverloggertrait.php';
	require_once BASE . 'traits/exceptionloggertrait.php';
	require_once BASE . 'traits/singleton.php';
	require_once BASE . 'traits/sapiloggertrait.php';
	require_once BASE . 'abstracts/abstractlogger.php';
	require_once BASE . 'nulllogger.php';
	require_once BASE . 'traits/loggerinterpolatortrait.php';
	require_once BASE . 'sapilogger.php';
	require_once BASE . 'linter.php';
	require_once BASE . 'shims.php';

	$logger = new SAPILogger();
	$logger->registerExceptionHandler();
	$logger->registerErrorHandler();

	$linter = new Linter($logger);
	$linter->ignoreDirs('./.git', './docs', './.github', './vendor');
	$linter->scanExts('php');
	$args = getopt('d:', ['dir:']);

	if (array_key_exists('dir', $args)) {
		$dir = $args['dir'];
	} elseif (array_key_exists('d', $args)) {
		$dir = $args['d'];
	} else {
		$dir = __DIR__;
	}

	if (! $linter->scan($dir)) {
		exit(1);
	}
}
