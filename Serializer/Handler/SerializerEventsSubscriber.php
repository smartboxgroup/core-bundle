<?php

namespace Smartbox\CoreBundle\Serializer\Handler;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Smartbox\CoreBundle\Type\SerializableInterface;

/**
 * Class SerializerEventsSubscriber.
 */
class SerializerEventsSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ['event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'],
            ['event' => 'serializer.pre_deserialize', 'method' => 'onPreDeserialize'],
        ];
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();

        $isArray = is_array($data) || (($data instanceof \SimpleXMLElement) && ($data->children()->count() > 0));

        if ($isArray && array_key_exists('_type', $data)) {
            if ($data instanceof \SimpleXMLElement) {
                $type = (string)$data->{'_type'};
            } else {
                $type = $data['_type'];
            }

            if (!empty($type) && is_a($type, SerializableInterface::class, true)) {
                $event->setType($type);
            }
        }
    }

    public function onPreSerialize(PreSerializeEvent $event)
    {
        $entity = $event->getObject();

        if (is_object($entity) && $entity instanceof SerializableInterface) {
            $event->setType($entity->getInternalType());
        }

        if (strpos(get_class($entity), 'Mock') !== false) {
            $event->setType(get_parent_class($entity));
        }
    }
}
