<?php
namespace Smartbox\CoreBundle\Entity\BasicTypes;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Integer
 * @package Smartbox\CoreBundle\Entity\BasicTypes
 */
class Integer extends Basic
{
    /**
     * @Assert\Type(type="integer")
     * @JMS\Type("integer")
     * @JMS\Groups({"logs"})
     * @var int
     */
    protected $value;

    /**
     * @param int $value
     */
    public function __construct($value = 0)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return (int)$this->value;
    }

    /***
     * @param int $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value)
    {
        if ((is_scalar($value) && is_numeric($value))) {
            $this->value = (int)$value;
        } elseif (is_object($value) && $value instanceof Integer) {
            $this->value = $value->getValue();
        } else {
            throw new \InvalidArgumentException("Expected integer");
        }
    }
}
