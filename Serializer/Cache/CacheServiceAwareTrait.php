<?php

namespace Smartbox\CoreBundle\Serializer\Cache;

use Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface;

trait CacheServiceAwareTrait
{
    /** @var CacheServiceInterface */
    private $cacheService;

    /**
     * @param CacheServiceInterface $cacheService
     */
    public function setCacheService(CacheServiceInterface $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * @return CacheServiceInterface
     */
    public function getCacheService()
    {
        return $this->cacheService;
    }
}
