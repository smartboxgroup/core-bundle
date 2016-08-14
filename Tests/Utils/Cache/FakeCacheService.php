<?php

namespace Smartbox\CoreBundle\Tests\Utils\Cache;

use Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface;

class FakeCacheService implements CacheServiceInterface
{
    private $cache = [];

    /** @var FakeCacheServiceSpy */
    private $spy;

    /**
     * @param FakeCacheServiceSpy $spy
     */
    public function __construct(FakeCacheServiceSpy $spy = null)
    {
        $this->spy = $spy;
    }

    /**
     * @param $method
     * @param array $arguments
     * @param null $result
     */
    private function notifySpy($method, $arguments = [], $result = null)
    {
        if (null !== $this->spy) {
            $this->spy->notify($method, $arguments, $result);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expireTTL = null)
    {
        $this->cache[$key] = serialize($value);

        $this->notifySpy('set', [$key, $value, $expireTTL], true);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $data = unserialize($this->cache[$key]);

        $this->notifySpy('get', [$key], $data);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key, $ttlLimit = null)
    {
        $result = array_key_exists($key, $this->cache);

        $this->notifySpy('exists', [$key], $result);

        return $result;
    }
}
