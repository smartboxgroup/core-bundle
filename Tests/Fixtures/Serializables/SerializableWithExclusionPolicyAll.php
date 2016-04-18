<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Serializables;

use JMS\Serializer\Annotation as JMS;

/**
 * Class SerializableWithExclusionPolicyAll.
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class SerializableWithExclusionPolicyAll extends SerializableWithoutExclusionPolicy
{
    /**
     * @JMS\Type("integer")
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
     *
     * @var string
     */
    protected $stringValue;
}
