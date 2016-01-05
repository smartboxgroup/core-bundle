<?php

namespace Smartbox\CoreBundle\Utils\Cache;

class CacheService implements CacheServiceInterface
{
    /**
     * @var \Predis\ClientInterface
     */
    protected $client;

    /**
     * @param \Predis\ClientInterface $client
     */
    public function __construct(\Predis\ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value, $expireTTL = null)
    {
        return $this->client->set($key, serialize($value), null, $expireTTL);
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        return unserialize($this->client->get($key));
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        return $this->client->exists($key);
    }
}