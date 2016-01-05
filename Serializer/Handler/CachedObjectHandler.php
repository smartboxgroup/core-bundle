<?php

namespace Smartbox\CoreBundle\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use Smartbox\CoreBundle\Serializer\Cache\CacheServiceAwareTrait;

class CachedObjectHandler implements SubscribingHandlerInterface
{
    const TYPE = 'CachedObject';

    use CacheServiceAwareTrait;

    public static function getDataCacheKey($data)
    {
        return sha1(serialize($data));
    }

    public static function getSubscribingMethods()
    {
        return array(
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => self::TYPE,
                'method' => 'getDataFromCache',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'xml',
                'type' => self::TYPE,
                'method' => 'getDataFromCache',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'array',
                'type' => self::TYPE,
                'method' => 'getDataFromCache',
            ),
            array(
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'mongo_array',
                'type' => self::TYPE,
                'method' => 'getDataFromCache',
            ),
        );
    }

    public function getDataFromCache(GenericSerializationVisitor $visitor, $data, array $type, Context $context)
    {
        return $this->getCacheService()->get(self::getDataCacheKey($data));
    }
}