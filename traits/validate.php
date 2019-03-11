<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait Validate
{
	final public static function isEmail(string $email): bool
	{
		return filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE) !== false;
	}

	final public static function isUrl(string $url): bool
	{
		return filter_var($url, FILTER_VALIDATE_URL, [
			'options' => [
				'flags' => [
					FILTER_FLAG_SCHEME_REQUIRED,
					FILTER_FLAG_HOST_REQUIRED,
				]
			]
		]) !== false;
	}
}
