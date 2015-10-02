<?php

namespace Smartbox\CoreBundle\Entity\Traits;

use JMS\Serializer\Annotation as JMS;

trait HasType
{
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
