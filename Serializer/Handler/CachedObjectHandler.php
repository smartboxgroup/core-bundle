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

    /**
     * Returns the caching key of the given data or null if it can not be cached.
     *
     * @param $data
     * @param Context $context
     *
     * @return null|string
     */
    public static function getDataCacheKey($data, Context $context)
    {
        $dataArray = [
            'data' => $data,
            'serializationFormat' => $context->getFormat(),
            'serializationGroups' => $context->attributes->get('groups'),
            'serializationVersion' => $context->attributes->get('version'),
        ];

        try {
            return sha1(serialize($dataArray));
        } catch (\Exception $e) {
            return;
        }
    }

    public static function getSubscribingMethods()
    {
        return [
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'json',
                'type' => self::TYPE,
                'method' => 'getDataFromCache',
            ],
            [
                'direction' => GraphNavigator::DIRECTION_SERIALIZATION,
                'format' => 'array',
                'type' => self::TYPE,
                'method' => 'getDataFromCache',
            ],
        ];
    }

    public function getDataFromCache(GenericSerializationVisitor $visitor, $data, array $type, Context $context)
    {
        $result = $this->getCacheService()->get(self::getDataCacheKey($data, $context));
        if ($visitor->getRoot() === null) {
            $visitor->setRoot($result);
        }

        return $result;
    }
}
