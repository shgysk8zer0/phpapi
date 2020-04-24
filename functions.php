<?php
namespace shgysk8zer0\PHPAPI;

function autoload(string $cname): void
{
	/**
	 * Strip out project namespace so loading from current directory
	 * shgysk8zer0\PHPAPI\Foo -> Foo -> ./foo.php
	 */
	$path = str_replace([__NAMESPACE__ . '\\'], [null], $cname);
	spl_autoload($path);
}
