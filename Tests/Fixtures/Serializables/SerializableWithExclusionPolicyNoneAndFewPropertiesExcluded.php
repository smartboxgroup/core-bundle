<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Serializables;

use JMS\Serializer\Annotation as JMS;

/**
 * Class SerializableWithExclusionPolicyNoneAndFewPropertiesExcluded.
 *
 * @JMS\ExclusionPolicy("NONE")
 */
class SerializableWithExclusionPolicyNoneAndFewPropertiesExcluded extends SerializableWithoutExclusionPolicy
{
    /**
     * @JMS\Type("integer")
     * @JMS\Exclude
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
     * @JMS\Exclude
     *
     * @var string
     */
    protected $stringValue;
}
