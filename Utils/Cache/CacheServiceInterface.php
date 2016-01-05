<?php

namespace Smartbox\CoreBundle\Utils\Cache;

interface CacheServiceInterface
{
    /**
     * @param $key
     * @param mixed $value
     * @param null $expireTTL
     * @return boolean
     */
    public function set($key, $value, $expireTTL = null);

    /**
     * @param $key
     * @return string
     */
    public function get($key);

    /**
     * @param $key
     * @return boolean
     */
    public function exists($key);
}