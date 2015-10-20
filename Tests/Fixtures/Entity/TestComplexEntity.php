<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Entity;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Type\Entity;
use Symfony\Component\Validator\Constraints as Assert;

class TestComplexEntity extends Entity
{
    /**
     * @JMS\Type("integer")
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @JMS\Since(EntityConstants::VERSION_2)
     * @Assert\Type(type="integer")
     * @Assert\NotBlank(groups={EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @var int
     */
    protected $integerValue;

    /**
     * @JMS\Type("double")
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @JMS\Since(EntityConstants::VERSION_1)
     * @Assert\Type(type="double")
     * @Assert\NotBlank(groups={EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @var double
     */
    protected $doubleValue;

    /**
     * @JMS\Type("string")
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @Assert\Type(type="string")
     * @Assert\NotBlank(groups={EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @var string
     */
    protected $stringValue;

    /**
     * @var Entity
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_A})
     * @JMS\Type("Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity")
     */
    protected $nestedEntity;

    /**
     * @Assert\Type(type="array")
     * @Assert\Valid
     * @JMS\Type("array<Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity>")
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_B})
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity[]
     */
    protected $arrayOfEntities = [];

    /**
     * @Assert\Type(type="array")
     * @Assert\Valid
     * @JMS\Type("array<integer>")
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_A})
     * @var integer[]
     */
    protected $arrayOfIntegers = [];

    /**
     * @Assert\Type(type="array")
     * @Assert\Valid
     * @JMS\Type("array<string>")
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @var string[]
     */
    protected $arrayOfStrings = [];

    /**
     * @Assert\Type(type="array")
     * @Assert\Valid
     * @JMS\Type("array<double>")
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_A})
     * @var double[]
     */
    protected $arrayOfDoubles = [];

    /**
     * @Assert\Type(type="array")
     * @Assert\Valid
     * @JMS\Type("array<DateTime>")
     * @JMS\Groups({EntityConstants::GROUP_A, EntityConstants::GROUP_B})
     * @var \DateTime[]
     */
    protected $arrayOfDates = [];

    public function __construct()
    {
        parent::__construct();
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
     * @return \Smartbox\CoreBundle\Type\Integer[]
     */
    public function getArrayOfIntegers()
    {
        return $this->arrayOfIntegers;
    }

    /**
     * @param \Smartbox\CoreBundle\Type\Integer[] $arrayOfIntegers
     */
    public function setArrayOfIntegers($arrayOfIntegers)
    {
        $this->arrayOfIntegers = $arrayOfIntegers;
    }

    /**
     * @return \Smartbox\CoreBundle\Type\String[]
     */
    public function getArrayOfStrings()
    {
        return $this->arrayOfStrings;
    }

    /**
     * @param \Smartbox\CoreBundle\Type\String[] $arrayOfStrings
     */
    public function setArrayOfStrings($arrayOfStrings)
    {
        $this->arrayOfStrings = $arrayOfStrings;
    }

    /**
     * @return \Smartbox\CoreBundle\Type\Double[]
     */
    public function getArrayOfDoubles()
    {
        return $this->arrayOfDoubles;
    }

    /**
     * @param \Smartbox\CoreBundle\Type\Double[] $arrayOfDoubles
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
}
