<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait DataValidation
{
	final public function isAllowedValue(string $key, string ...$allowed_values): bool
	{
		return $this->has($key) and in_array($this->get($key, false), $allowed_values);
	}

	final public function isEmail(string $key): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_EMAIL);
	}

	final public function isUrl(string $key): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_URL, [
			'flags' => FILTER_FLAG_SCHEME_REQUIRED | FILTER_FLAG_HOST_REQUIRED,
		]);
	}

	final public function isInt(string $key, int $min = PHP_INT_MIN, int $max = PHP_INT_MAX): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_INT, [
			'options' => [
				'min_range' => $min,
				'max_range' => $max,
			],
		]);
	}

	final public function isFloat(string $key, float $min = PHP_FLOAT_MIN, float $max = PHP_FLOAT_MAX): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_FLOAT, [
			'options' => [
				'min_range' => $min,
				'max_range' => $max,
			],
		]);
	}

	final public function isIp(string $key): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_IP);
	}

	final public function isPublicIp(string $key): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	final public function isIp4(string $key): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
	}

	final public function isPublicIp4(string $key): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	final public function isIp6(string $key): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
	}

	final public function isPublicIp6(string $key): bool
	{
		return $this->has($key) and filter_var($this->get($key, false), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
	}

	final public function matches(string $key, string $pattern): bool
	{
		return $this->has($key) and preg_match($pattern, $this->get($key));
	}

	abstract public function get(
		string $key,
		bool   $escape  = true,
		string $default = null,
		string $charset = 'UTF-8'
	);

	abstract public function has(string ...$keys): bool;
}
