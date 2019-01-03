<?php

namespace Smartbox\CoreBundle\Serializer\Cache;

use JMS\Serializer\AbstractVisitor;
use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\ObjectEvent;
use Smartbox\CoreBundle\Serializer\Handler\CachedObjectHandler;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;

class CacheEventsSubscriber implements EventSubscriberInterface
{
    use CacheServiceAwareTrait;

    const KEY_EXISTS_TIMEOUT = 60;

    const DATA_PROPERTY = 'data';

    /** @var array */
    private $reflectors = [];

    public function __construct(array $vistorClasses)
    {
        foreach ($vistorClasses as $class) {
            $reflector = new \ReflectionProperty($class, self::DATA_PROPERTY);
            $reflector->setAccessible(true);
            $this->reflectors[$class] = $reflector;
        }
    }

    public static function getSubscribedEvents()
    {
        $formats = ['json', 'array'];

        $config = [];
        foreach ($formats as $format) {
            $config[] = ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize', 'format' => $format];
            $config[] = [
                'event' => 'serializer.post_serialize',
                'method' => 'onPostSerialize',
                'format' => $format,
            ];
        }

        return $config;
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        $data = $event->getObject();

        if (!$data instanceof SerializerCacheableInterface) {
            return;
        }

        $visitor = $event->getVisitor();
        $visitorClass = \get_class($visitor);

        if (\array_key_exists($visitorClass, $this->reflectors)) {
            $key = CachedObjectHandler::getDataCacheKey($data, $event->getContext());
            if (null !== $key && $this->getCacheService()->exists($key, self::KEY_EXISTS_TIMEOUT)) {
                $event->setType(CachedObjectHandler::TYPE);
            }
        }
    }

    public function onPostSerialize(ObjectEvent $event)
    {
        $object = $event->getObject();
        $typeName = $event->getType()['name'];

        if (CachedObjectHandler::TYPE === $typeName || !$object instanceof SerializerCacheableInterface) {
            return;
        }

        $visitor = $event->getVisitor();
        $visitorClass = \get_class($visitor);

        if (\array_key_exists($visitorClass, $this->reflectors)) {
            // save to cache
            $cacheData = $this->getDataFromVisitor($visitor, $visitorClass);
            $key = CachedObjectHandler::getDataCacheKey($object, $event->getContext());

            if (null !== $key) {
                $this->cacheService->set($key, $cacheData);
            }
        }
    }

    /**
     * @param AbstractVisitor $visitor
     * @param string          $class
     *
     * @return mixed
     */
    private function getDataFromVisitor(AbstractVisitor $visitor, string $class)
    {
        return $this->reflectors[$class]->getValue($visitor);
    }
}
