<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Serializables;

use JMS\Serializer\Annotation as JMS;

/**
 * Class SerializableWithExclusionPolicyAll
 * @package Smartbox\CoreBundle\Tests\Fixtures\Serializables
 *
 * @JMS\ExclusionPolicy("ALL")
 */
class SerializableWithExclusionPolicyAll extends SerializableWithoutExclusionPolicy
{
    /**
     * @JMS\Type("integer")
     * @var int
     */
    protected $integerValue;

    /**
     * @JMS\Type("double")
     * @var double
     */
    protected $doubleValue;

    /**
     * @JMS\Type("string")
     * @var string
     */
    protected $stringValue;
}