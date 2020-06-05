<?php

namespace shgysk8zer0\PHPAPI\Traits;

use \shgysk8zer0\PHPAPI\Interfaces\{CacheInterface};

trait CacheAwareTrait
{
	protected $cache = null;

	final public function setCache(CacheInterface $cache): void
	{
		$this->cache = $cache;
	}
}
