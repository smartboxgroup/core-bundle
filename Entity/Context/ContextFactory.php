<?php

namespace Smartbox\CoreBundle\Entity\Context;

use JMS\Serializer\Context;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use Smartbox\CoreBundle\Entity\Entity;

class ContextFactory
{
    /**
     * @param Context $context
     * @param string $group
     * @param string $version
     * @return Context
     */
    protected static function prepareContextForFixtures(Context $context, $group, $version)
    {
        if (!is_null($version)) {
            $context->setVersion($version);
        }

        if (!is_null($group)) {
            $context->setGroups([$group, Entity::GROUP_METADATA]);
        }

        return $context;
    }

    /**
     * @param $group
     * @param $version
     * @return SerializationContext
     */
    public static function createSerializationContextForFixtures($group, $version)
    {
        return self::prepareContextForFixtures(new SerializationContext(), $group, $version);
    }

    /**
     * @param $group
     * @param $version
     * @return DeserializationContext
     */
    public static function createDeserializationContextForFixtures($group, $version)
    {
        return self::prepareContextForFixtures(new DeserializationContext(), $group, $version);
    }
}