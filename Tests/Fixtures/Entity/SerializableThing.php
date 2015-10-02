<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Entity;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Entity\Entity;
use Smartbox\CoreBundle\Entity\SerializableInterface;
use Smartbox\CoreBundle\Entity\Traits\HasType;

/**
 * Class SerializableThing
 * @package Smartbox\CoreBundle\Tests\Fixtures\Entity
 */
class SerializableThing implements SerializableInterface
{
    use HasType;

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
     * @var Entity
     * @JMS\Type("Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity")
     */
    protected $nestedEntity;

    /**
     * @JMS\Type("array<Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity>")
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity[]
     */
    protected $arrayOfEntities = [];

    /**
     * @JMS\Type("array<Smartbox\CoreBundle\Entity\BasicTypes\Integer>")
     * @var \Smartbox\CoreBundle\Entity\BasicTypes\Integer[]
     */
    protected $arrayOfIntegers = [];

    /**
     * @JMS\Type("array<Smartbox\CoreBundle\Entity\BasicTypes\String>")
     * @var \Smartbox\CoreBundle\Entity\BasicTypes\String[]
     */
    protected $arrayOfStrings = [];

    /**
     * @JMS\Type("array<Smartbox\CoreBundle\Entity\BasicTypes\Double>")
     * @var \Smartbox\CoreBundle\Entity\BasicTypes\Double[]
     */
    protected $arrayOfDoubles = [];

    /**
     * @JMS\Type("array<Smartbox\CoreBundle\Entity\BasicTypes\Date>")
     * @var \Smartbox\CoreBundle\Entity\BasicTypes\Date[]
     */
    protected $arrayOfDates = [];

    public function __construct()
    {
    }

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
     * @param float $doubleValue
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

    /**
     * @return Entity
     */
    public function getNestedEntity()
    {
        return $this->nestedEntity;
    }

    /**
     * @param Entity $nestedEntity
     */
    public function setNestedEntity($nestedEntity)
    {
        $this->nestedEntity = $nestedEntity;
    }

    /**
     * @return \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity[]
     */
    public function getArrayOfEntities()
    {
        return $this->arrayOfEntities;
    }

    /**
     * @param \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity[] $arrayOfEntities
     */
    public function setArrayOfEntities($arrayOfEntities)
    {
        $this->arrayOfEntities = $arrayOfEntities;
    }

    /**
     * @return \Smartbox\CoreBundle\Entity\BasicTypes\Integer[]
     */
    public function getArrayOfIntegers()
    {
        return $this->arrayOfIntegers;
    }

    /**
     * @param \Smartbox\CoreBundle\Entity\BasicTypes\Integer[] $arrayOfIntegers
     */
    public function setArrayOfIntegers($arrayOfIntegers)
    {
        $this->arrayOfIntegers = $arrayOfIntegers;
    }

    /**
     * @return \Smartbox\CoreBundle\Entity\BasicTypes\String[]
     */
    public function getArrayOfStrings()
    {
        return $this->arrayOfStrings;
    }

    /**
     * @param \Smartbox\CoreBundle\Entity\BasicTypes\String[] $arrayOfStrings
     */
    public function setArrayOfStrings($arrayOfStrings)
    {
        $this->arrayOfStrings = $arrayOfStrings;
    }

    /**
     * @return \Smartbox\CoreBundle\Entity\BasicTypes\Double[]
     */
    public function getArrayOfDoubles()
    {
        return $this->arrayOfDoubles;
    }

    /**
     * @param \Smartbox\CoreBundle\Entity\BasicTypes\Double[] $arrayOfDoubles
     */
    public function setArrayOfDoubles($arrayOfDoubles)
    {
        $this->arrayOfDoubles = $arrayOfDoubles;
    }

    /**
     * @return \Smartbox\CoreBundle\Entity\BasicTypes\Date[]
     */
    public function getArrayOfDates()
    {
        return $this->arrayOfDates;
    }

    /**
     * @param \Smartbox\CoreBundle\Entity\BasicTypes\Date[] $arrayOfDates
     */
    public function setArrayOfDates($arrayOfDates)
    {
        $this->arrayOfDates = $arrayOfDates;
    }
}
