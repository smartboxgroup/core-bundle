<?php
namespace Smartbox\CoreBundle\Type;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Double
 * @package Smartbox\CoreBundle\Type
 */
class Double extends Basic
{
    /**
     * @Assert\Type(type="double")
     * @JMS\Type("double")
     * @JMS\Groups({"logs"})
     * @var double
     */
    protected $value = 0.0;

    /**
     * @param double $value
     */
    public function __construct($value = 0.0)
    {
        $this->value = $value;
    }

    /**
     * @return double
     */
    public function getValue()
    {
        return (double)$this->value;
    }

    /***
     * @param double $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value)
    {
        if ((is_scalar($value) && is_numeric($value))) {
            $this->value = (double)$value;
        } elseif (is_object($value) && $value instanceof Double) {
            $this->value = $value->getValue();
        } else {
            throw new \InvalidArgumentException("Expected double");
        }
    }
}
