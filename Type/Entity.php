<?php

namespace Smartbox\CoreBundle\Type;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Serializer\Cache\SerializerCacheableInterface;
use Smartbox\CoreBundle\Type\Traits\HasEntityGroup;
use Smartbox\CoreBundle\Type\Traits\HasInternalType;
use Smartbox\CoreBundle\Type\Traits\HasAPIVersion;

class Entity implements EntityInterface //, SerializerCacheableInterface
{
    use HasEntityGroup;
    use HasAPIVersion;
    use HasInternalType;

    public function __construct()
    {
    }
}