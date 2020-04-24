<?php
namespace shgysk8zer0\PHPAPI;

use \shgysk8zer0\PHPAPI\{Linter, SAPILogger};
use \LogicException;
use \RuntimeException;

const CONFIG = __DIR__ . DIRECTORY_SEPARATOR . 'config.ini';

if (PHP_SAPI !== 'cli') {
	http_response_code(403);
	exit();
} elseif (! isset($argv) or ! is_array($argv) or realpath($argv[0])!== __FILE__) {
	throw new LogicException('Linter must be called directly');
} elseif (! file_exists(CONFIG)) {
	throw new RuntimeException('Config file not found');
} elseif (! $config = parse_ini_file(CONFIG, true, INI_SCANNER_TYPED)) {
	throw new RuntimeException('Error parsing config file');
} else {
	date_default_timezone_set($config['init']['timezone']);

	foreach ($config['init']['require'] as $path) {
		require_once $path;
	}

	set_include_path(join(PATH_SEPARATOR, $config['autoload']['path']));
	spl_autoload_register($config['autoload']['function']);
	spl_autoload_extensions(join(',', $config['autoload']['extensions']));

	$linter = new Linter();
	$linter->setLogger(new SAPILogger());
	$linter->ignoreDirs(...$config['lint']['ignore']);
	$linter->scanExts(...$config['lint']['extensions']);

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
