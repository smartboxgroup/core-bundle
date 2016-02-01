<?php

namespace Smartbox\CoreBundle\Type\Traits;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Type\EntityInterface;

trait HasInternalType
{
    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("_type")
     * @JMS\Type("string")
     * @JMS\Groups({EntityInterface::GROUP_METADATA})
     * @return string
     */
    public function getInternalType()
    {
        return get_class($this);
    }
}
