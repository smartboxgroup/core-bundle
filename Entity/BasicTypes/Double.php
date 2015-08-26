<?php
namespace Smartbox\CoreBundle\Entity\BasicTypes;


use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

class Double extends Basic
{

    /**
     * @Assert\Type(type="double")
     * @JMS\Type("double")
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
            throw \InvalidArgumentException("Expected double");
        }
    }

}