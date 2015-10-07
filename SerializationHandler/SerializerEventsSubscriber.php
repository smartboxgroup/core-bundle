<?php
namespace Smartbox\CoreBundle\SerializationHandler;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface;
use JMS\Serializer\EventDispatcher\PreDeserializeEvent;
use JMS\Serializer\EventDispatcher\PreSerializeEvent;
use Smartbox\CoreBundle\Type\SerializableInterface;

/**
 * Class SerializerEventsSubscriber
 * @package Smartbox\CoreBundle\SerializationHandler
 */
class SerializerEventsSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            array('event' => 'serializer.pre_serialize', 'method' => 'onPreSerialize'),
            array('event' => 'serializer.pre_deserialize', 'method' => 'onPreDeserialize'),
        );
    }

    public function onPreDeserialize(PreDeserializeEvent $event)
    {
        $data = $event->getData();

        $isArray = is_array($data) || (($data instanceof \SimpleXMLElement) && ($data->children()->count() > 0));

        if ($isArray && array_key_exists('type', $data)) {
            if ($data instanceof \SimpleXMLElement) {
                $type = (string)$data->{'type'};
            } else {
                $type = $data['type'];
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
            $event->setType($entity->getType());
        }

        if(strpos(get_class($entity),'Mock') !== FALSE){
            $event->setType(get_parent_class($entity));
        }
    }
}
