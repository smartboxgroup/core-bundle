<?php

namespace Smartbox\CoreBundle\Serializer\Handler;

use JMS\Serializer\Context;
use JMS\Serializer\GenericSerializationVisitor;
use JMS\Serializer\GraphNavigator;
use JMS\Serializer\Handler\SubscribingHandlerInterface;
use JMS\Serializer\SerializationContext;
use Smartbox\CoreBundle\Serializer\Cache\CacheServiceAwareTrait;

class CachedObject
{
    protected $data;
    protected $serializationFormat;
    protected $serializationGroups = [];
    protected $serializationVersion;

    /**
     * @param $data
     * @param Context $context
     */
    public function __construct($data, Context $context)
    {
        $this->data = $data;
        $this->serializationFormat = $context->getFormat();
        $this->serializationGroups = $context->attributes->get('groups');
        $this->serializationVersion = $context->attributes->get('version');
    }
}