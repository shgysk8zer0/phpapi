<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait Data
{
	protected static $_data = [];

	final protected static function _setInputData(array $inputs = [])
	{
		static::$_data = $inputs;
	}

	final public function get(
		string $key,
		bool   $escape  = true,
		string $default = null,
		string $charset = 'UTF-8'
	)
	{
		if (! $this->has($key)) {
			return $default;
		} elseif ($escape) {
			return htmlspecialchars(static::$_data[$key], ENT_COMPAT | ENT_HTML5, $charset);
		} else {
			return static::$_data[$key];
		}
	}

	final public function has(string ...$keys): bool
	{
		$has = true;
		foreach ($keys as $key) {
			if (! array_key_exists($key, static::$_data)) {
				$has = false;
				break;
			}
		}
		return $has;
	}
}
