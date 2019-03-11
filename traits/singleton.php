<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait Singleton
{
	private static $_instance = null;

	final public static function getInstance(): self
	{
		if (is_null(static::$_instance)) {
			static::$_instance = new self();
		}
		return static::$_instance;
	}
}
