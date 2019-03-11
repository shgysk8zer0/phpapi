<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait Sanitize
{
	final public static function sanitizeEmail(string $email): string
	{
		return filter_var($email, FILTER_SANITIZE_EMAIL);
	}

	final public static function sanitizeUrl(string $url): string
	{
		return filter_var($url, FILTER_SANITIZE_URL);
	}

	final public static function sanitizeHtml(string $text): string
	{
		return filter_var($text, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
	}
}
