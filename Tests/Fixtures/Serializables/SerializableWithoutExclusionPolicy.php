<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Serializables;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Type\SerializableInterface;
use Smartbox\CoreBundle\Type\Traits\HasInternalType;

/**
 * Class SerializableWithoutExclusionPolicy
 * @package Smartbox\CoreBundle\Tests\Fixtures\Serializables
 */
class SerializableWithoutExclusionPolicy implements SerializableInterface
{
    use HasInternalType;

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

    /**
     * @return int
     */
    public function getIntegerValue()
    {
        return $this->integerValue;
    }

    /**
     * @param int $integerValue
     */
    public function setIntegerValue($integerValue)
    {
        $this->integerValue = $integerValue;
    }

    /**
     * @return float
     */
    public function getDoubleValue()
    {
        return $this->doubleValue;
    }

    /**
     * @param double $doubleValue
     */
    public function setDoubleValue($doubleValue)
    {
        $this->doubleValue = $doubleValue;
    }

    /**
     * @return string
     */
    public function getStringValue()
    {
        return $this->stringValue;
    }

    /**
     * @param string $stringValue
     */
    public function setStringValue($stringValue)
    {
        $this->stringValue = $stringValue;
    }
}