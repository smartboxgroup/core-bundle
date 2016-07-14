<?php

namespace Smartbox\CoreBundle\Utils\Cache;

/**
 * Class NullCacheService.
 */
class NullCacheService implements CacheServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expireTTL = 1800)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key, $ttlLimit = null)
    {
        return false;
    }
}
