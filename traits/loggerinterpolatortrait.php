<?php
namespace shgysk8zer0\PHPAPI\Traits;

trait LoggerInterpolatorTrait
{
	/**
	 * Provides a simple, reusable, standard way of generating messages for PSR-3 Loggers
	 * @param  string $message The given message with `{placeholder}`s
	 * @param  array  $context Additional context, keys referrer to {placeholder}s
	 * @return string          The resulting string
	 */
	final public function interpolate(string $message, array $context = []): string
	{
		if (count($context) !== 0) {
			$keys = array_map(function(string $key): string
			{
				return "{{$key}}";
			}, array_keys($context));

			return strtr($message, array_combine($keys, array_values($context)));
		} else {
			return $message;
		}
	}
}
