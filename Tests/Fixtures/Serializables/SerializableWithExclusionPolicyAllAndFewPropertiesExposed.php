<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Serializables;

use JMS\Serializer\Annotation as JMS;

/**
 * Class SerializableWithExclusionPolicyAllAndFewPropertiesExposed.
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class SerializableWithExclusionPolicyAllAndFewPropertiesExposed extends SerializableWithoutExclusionPolicy
{
    /**
     * @JMS\Type("integer")
     * @JMS\Expose
     *
     * @var int
     */
    protected $integerValue;

    /**
     * @JMS\Type("double")
     *
     * @var float
     */
    protected $doubleValue;

    /**
     * @JMS\Type("string")
     * @JMS\Expose
     *
     * @var string
     */
    protected $stringValue;
}
