<?php
namespace shgysk8zer0\PHPAPI;

final class CSP
{
	private $_policy = [];

	private $_report_only = false;

	private $_report_to = null;

	final public function __construct(string $default_src = 'none')
	{
		$this->_set('default-src', $default_src);
	}

	final public function send(): void
	{
		if (headers_sent()) {
			trigger_error('Attempting to set CSP after headers sent');
		} else {
			$csp = array_map(function(string $key, $val): string
			{
				if (is_string($val)) {
					return "{$key} {$val}";
				} elseif (is_null($val)) {
					return $key;
				}
			}, array_keys($this->_policy), array_values($this->_policy));

			if ($this->_report_only) {
				header('Content-Security-Policy-Report-Only: ' . join('; ', $csp));
			} else {
				header('Content-Security-Policy: ' . join('; ', $csp));
			}
			if (isset($this->_report_to)) {
				header('Report-To: ' . json_encode($this->_report_to));
			}
		}
	}

	final public function __debugInfo(): array
	{
		return [
			'reportOnly' => $this->_report_only,
			'reportTo'   => $this->_report_to,
			'policy'     =>$this->_policy,
		];
	}

	final public function defaultSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('default-src', join(' ', $srcs));
		return $this;
	}

	final public function scriptSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('script-src', join(' ', $srcs));
		return $this;
	}

	final public function styleSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('style-src', join(' ', $srcs));
		return $this;
	}

	final public function imgSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('img-src', join(' ', $srcs));
		return $this;
	}

	final public function mediaSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('media-src', join(' ', $srcs));
		return $this;
	}

	final public function fontSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('font-src', join(' ', $srcs));
		return $this;
	}

	final public function connectSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('connect-src', join(' ', $srcs));
		return $this;
	}

	final public function frameSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('frame-src', join(' ', $srcs));
		return $this;
	}

	final public function objectSrc(string ...$srcs): self
	{
		if (empty($srcs)) {
			$srcs = ['none'];
		}
		$this->_set('object-src', join(' ', $srcs));
		return $this;
	}

	final public function reportUri(string ...$srcs): self
	{
		$this->_set('report-uri', join(' ', $srcs));
		return $this;
	}

	final public function blockAllMixedContent(bool $block = true): self
	{
		if ($block) {
			$this->_set('block-all-mixed-content');
		} else {
			$this->_rm('block-all-mixed-content');
		}
		return $this;
	}

	final public function upgradeInsecureRequests(bool $upgrade = true): self
	{
		if ($upgrade) {
			$this->_set('upgrade-insecure-requests');
		} else {
			$this->_rm('upgrade-insecure-requests');
		}
		return $this;
	}

	final public function reportOnly(bool $report_only = true): self
	{
		$this->_report_only = $report_only;
		return $this;
	}

	final public function reportTo(object $report_to): self
	{
		$this->_report_to = $report_to;
		return $this;
	}

	final private function _set(string $key, string $value = null): void
	{
		if (is_string($value)) {
			$this->_policy[$key] = str_replace([
				'self',
				'none',
				'unsafe-inline',
				'unsafe-eval',
				'strict-dynamic',
			], [
				"'self'",
				"'none'",
				"'unsafe-inline'",
				"'unsafe-eval'",
				"'strict-dynamic'",
			], $value);
		} else {
			$this->_policy[$key] = $value;
		}
	}

	final public function _rm(string $key)
	{
		unset($this->_policy[$key]);
	}
}
