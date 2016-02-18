<?php

namespace Smartbox\CoreBundle\Serializer\Cache;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use JMS\Serializer\GenericSerializationVisitor;
use Smartbox\CoreBundle\Serializer\Handler\CachedObjectHandler;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class CacheEventsSubscriber implements EventSubscriberInterface
{
    use CacheServiceAwareTrait;

    /** @var \ReflectionProperty */
    private $dataProperty;

    public function __construct()
    {
        $this->dataProperty = new \ReflectionProperty(GenericSerializationVisitor::class, 'data');
        $this->dataProperty->setAccessible(true);
    }

    public static function getSubscribedEvents()
    {
        $formats = ['json', 'array'];

        $config = [];
        foreach ($formats as $format) {
            $config[] = array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'format' => $format);
            $config[] = array('event' => 'serializer.post_serialize', 'method' => 'onPostSerialize', 'format' => $format);
        }

        return $config;
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        $data = $event->getObject();
        if ($data instanceof SerializerCacheableInterface && $event->getVisitor() instanceof GenericSerializationVisitor) {
            $key = CachedObjectHandler::getDataCacheKey($data, $event->getContext());
            if ($key !== null && $this->getCacheService()->exists($key)) {
                $event->setType(CachedObjectHandler::TYPE);
            }
        }
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $visitor = $event->getVisitor();
        $object = $event->getObject();
        $type = $event->getType();

        if ($type['name'] !== CachedObjectHandler::TYPE && $object instanceof SerializerCacheableInterface && $visitor instanceof GenericSerializationVisitor){
            // save to cache
            $cacheData = $this->getDataFromVisitor($visitor);
            $key = CachedObjectHandler::getDataCacheKey($object, $event->getContext());

            if($key !== null){
                $this->cacheService->set($key, $cacheData);
            }
        }
    }

    /**
     * @param GenericSerializationVisitor $visitor
     * @return array
     */
    private function getDataFromVisitor(GenericSerializationVisitor $visitor)
    {
        return $this->dataProperty->getValue($visitor);
    }
}