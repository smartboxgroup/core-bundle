<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Entity;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Type\Entity;
use Smartbox\CoreBundle\Type\SerializableInterface;
use Smartbox\CoreBundle\Type\Traits\HasInternalType;

/**
 * Class SerializableThing.
 */
class SerializableThing implements SerializableInterface
{
    use HasInternalType;

    /**
     * @JMS\Type("integer")
     * @JMS\Expose
     *
     * @var int
     */
    protected $integerValue;

    /**
     * @JMS\Type("double")
     * @JMS\Expose
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

    /**
     * @var Entity
     * @JMS\Type("Smartbox\\Tests\Fixtures\Entity\TestEntity")
     * @JMS\Expose
     */
    protected $nestedEntity;

    /**
     * @JMS\Type("array<Smartbox\\Tests\Fixtures\Entity\TestEntity>")
     * @JMS\Expose
     *
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity[]
     */
    protected $arrayOfEntities = [];

    /**
     * @JMS\Type("array<Smartbox\\Type\IntegerType>")
     * @JMS\Expose
     *
     * @var \Smartbox\CoreBundle\Type\IntegerType[]
     */
    protected $arrayOfIntegers = [];

    /**
     * @JMS\Type("array<Smartbox\\Type\StringType>")
     * @JMS\Expose
     *
     * @var \Smartbox\CoreBundle\Type\StringType[]
     */
    protected $arrayOfStrings = [];

    /**
     * @JMS\Type("array<Smartbox\\Type\DoubleType>")
     * @JMS\Expose
     *
     * @var \Smartbox\CoreBundle\Type\DoubleType[]
     */
    protected $arrayOfDoubles = [];

    /**
     * @JMS\Type("array<Smartbox\\Type\Date>")
     * @JMS\Expose
     *
     * @var \Smartbox\CoreBundle\Type\Date[]
     */
    protected $arrayOfDates = [];

    /**
     * @JMS\Type("array<DateTime>")
     * @JMS\Expose
     *
     * @var \DateTime[]
     */
    protected $arrayOfDateTimes = [];

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
     * @return \Smartbox\CoreBundle\Type\IntegerType[]
     */
    public function getArrayOfIntegers()
    {
        return $this->arrayOfIntegers;
    }

    /**
     * @param \Smartbox\CoreBundle\Type\IntegerType[] $arrayOfIntegers
     */
    public function setArrayOfIntegers($arrayOfIntegers)
    {
        $this->arrayOfIntegers = $arrayOfIntegers;
    }

    /**
     * @return \Smartbox\CoreBundle\Type\StringType[]
     */
    public function getArrayOfStrings()
    {
        return $this->arrayOfStrings;
    }

    /**
     * @param \Smartbox\CoreBundle\Type\StringType[] $arrayOfStrings
     */
    public function setArrayOfStrings($arrayOfStrings)
    {
        $this->arrayOfStrings = $arrayOfStrings;
    }

    /**
     * @return \Smartbox\CoreBundle\Type\DoubleType[]
     */
    public function getArrayOfDoubles()
    {
        return $this->arrayOfDoubles;
    }

    /**
     * @param \Smartbox\CoreBundle\Type\DoubleType[] $arrayOfDoubles
     */
    public function setArrayOfDoubles($arrayOfDoubles)
    {
        $this->arrayOfDoubles = $arrayOfDoubles;
    }

    /**
     * @return \Smartbox\CoreBundle\Type\Date[]
     */
    public function getArrayOfDates()
    {
        return $this->arrayOfDates;
    }

    /**
     * @param \Smartbox\CoreBundle\Type\Date[] $arrayOfDates
     */
    public function setArrayOfDates($arrayOfDates)
    {
        $this->arrayOfDates = $arrayOfDates;
    }

    /**
     * @return \DateTime[]
     */
    public function getArrayOfDateTimes()
    {
        return $this->arrayOfDateTimes;
    }

    /**
     * @param \DateTime[] $arrayOfDateTimes
     */
    public function setArrayOfDateTimes(array $arrayOfDateTimes)
    {
        $this->arrayOfDateTimes = $arrayOfDateTimes;
    }
}
