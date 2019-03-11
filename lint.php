<?php
namespace shgysk8zer0\PHPAPI;
use \shgysk8zer0\PHPAPI\{Linter, Headers};
use \shgysk8zer0\PHPAPI\Abstracts\{HTTPSTatusCodes as HTTP};

set_include_path(dirname(__DIR__, 2));
spl_autoload_register('spl_autoload');
require_once __DIR__ . DIRECTORY_SEPARATOR . 'shims.php';

if (PHP_SAPI !== 'cli') {
	Headers::status(HTTP::FORBIDDEN);
	exit();
} else {
	$linter = new Linter();
	$linter->ignoreDirs('./.git', './docs', './.github');
	$linter->scanExts('php');

	if (! $linter->scan(__DIR__)) {
		exit(1);
	}
}
