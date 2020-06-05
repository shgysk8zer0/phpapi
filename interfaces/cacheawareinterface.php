<?php

namespace shgysk8zer0\PHPAPI\Interfaces;

use \Serializable;

/**
 * Extend serializable to ensure that custom serialization is implemented
 * to prevent cache itself from being cached
 */
interface CacheAwareInterface extends Serializable
{
	public function setCache(CacheInterface $cache): void;
}
