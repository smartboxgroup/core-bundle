<?php

namespace Smartbox\CoreBundle\Type\Traits;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Type\EntityInterface;

trait HasType
{
    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("type")
     * @JMS\Type("string")
     * @JMS\Groups({EntityInterface::GROUP_METADATA})
     * @return string
     */
    public function getType()
    {
        return get_class($this);
    }
}
