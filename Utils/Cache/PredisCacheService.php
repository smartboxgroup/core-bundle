<?php

namespace Smartbox\CoreBundle\Utils\Cache;

use Predis\PredisException;

/**
 * Class PredisCacheService.
 */
class PredisCacheService implements CacheServiceInterface
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
    public function set($key, $value, $expireTTL = 1800)
    {
        if (!$key) {
            throw new \InvalidArgumentException('The key should not be null');
        }

        try {
            if ($expireTTL) {
                if (!is_integer($expireTTL)) {
                    throw new \InvalidArgumentException(
                        sprintf('Expire TTL should be integer value. Given: %s', json_encode($expireTTL))
                    );
                }

                if (!($expireTTL > 0)) {
                    throw new \InvalidArgumentException(
                        sprintf('Expire TTL should be higher than 0. Given: %s', $expireTTL)
                    );
                }

                return $this->client->set($key, serialize($value), 'EX', $expireTTL);
            }

            return $this->client->set($key, serialize($value));
        } catch (PredisException $e) {
            $this->logException($e);

            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        if (!$key) {
            throw new \InvalidArgumentException('The key should not be null');
        }

        try {
            return unserialize($this->client->get($key));
        } catch (PredisException $ex) {
            $this->logException($ex);

            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function exists($key, $ttlLimit = null)
    {
        if (!$key) {
            throw new \InvalidArgumentException('The key should not be null');
        }

        try {
            if ($this->client->exists($key)) {
                if (!is_null($ttlLimit)) {
                    return $this->client->ttl($key) >= $ttlLimit;
                }

                return true;
            }

            return false;
        } catch (PredisException $ex) {
            $this->logException($ex);

            return false;
        }
    }

    /**
     * Errors with redis should be logged but should not interrupt the execution. On the other hand Redis is a service
     * which might be used anywhere, even by the logger. Therefore, to prevent bigger problems to log this error with use
     * the simple native php function error_log with a simple message.
     *
     * @param PredisException $ex
     */
    protected function logException(PredisException $ex)
    {
        error_log('Error: Redis service is down: '.$ex->getMessage());
    }
}
