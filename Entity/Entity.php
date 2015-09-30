<?php

namespace Smartbox\CoreBundle\Entity;


use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Entity\Traits\HasGroup;
use Smartbox\CoreBundle\Entity\Traits\HasVersion;

class Entity implements EntityInterface
{
    use HasGroup;
    use HasVersion;

    public function __construct()
    {
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("type")
     * @JMS\Type("string")
     * @JMS\Groups({Entity::GROUP_METADATA})
     * @return string
     */
    public function getType()
    {
        return get_class($this);
    }
}