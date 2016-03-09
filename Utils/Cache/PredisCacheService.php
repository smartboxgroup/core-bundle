<?php

namespace Smartbox\CoreBundle\Utils\Cache;

use Predis\Connection\ConnectionException;
use Psr\Log\LoggerAwareTrait;

/**
 * Class PredisCacheService
 * @package Smartbox\CoreBundle\Utils\Cache
 */
class PredisCacheService implements CacheServiceInterface
{
    use LoggerAwareTrait;

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
    public function set($key, $value, $expireTTL = 1800)
    {
        if(!$key){
            return false;
        }

        try {
            if ($expireTTL) {
                if (!is_integer($expireTTL)) {
                    throw new \RuntimeException(sprintf('Expire TTL should be integer value. Given: "%s"', $expireTTL));
                }

                if (! $expireTTL > 0) {
                    throw new \RuntimeException(sprintf('Expire TTL should be higher than 0. Given: "%s"', $expireTTL));
                }

                return $this->client->set($key, serialize($value), 'EX', $expireTTL);
            } else {
                return $this->client->set($key, serialize($value));
            }
        } catch (ConnectionException $e) {
            $this->logger->error('Redis service is down.', ['exception' => $e]);

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if(!$key){
            throw new \InvalidArgumentException("The key should not be null");
        }

        return unserialize($this->client->get($key));
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key)
    {
        if(!$key){
            return false;
        }

        try {
            return $this->client->exists($key) && $this->client->ttl($key) > 60;
        } catch (ConnectionException $e) {
            $this->logger->error('Redis service is down.', ['exception' => $e]);

            return false;
        }
    }
}