<?php

namespace Smartbox\CoreBundle\Utils\Cache;

interface CacheServiceInterface
{
    /**
     * @param $key
     * @param mixed $value
     * @param null $expireTTL
     *
     * @return bool
     */
    public function set($key, $value, $expireTTL = null);

    /**
     * @param $key
     *
     * @return string
     */
    public function get($key);

    /**
     * @param $key
     * @param $ttlLimit
     *
     * @return bool
     */
    public function exists($key, $ttlLimit = null);
}
