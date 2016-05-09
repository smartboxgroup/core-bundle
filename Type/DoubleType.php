<?php

namespace Smartbox\CoreBundle\Type;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class DoubleType.
 */
class DoubleType extends Basic
{
    /**
     * @Assert\Type(type="double")
     * @JMS\Type("double")
     * @JMS\Expose
     * @JMS\Groups({"logs"})
     *
     * @var float
     */
    protected $value = 0.0;

    /**
     * @param float $value
     */
    public function __construct($value = 0.0)
    {
        $this->value = $value;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return (double) $this->value;
    }

    /***
     * @param double $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value)
    {
        if ((is_scalar($value) && is_numeric($value))) {
            $this->value = (double) $value;
        } elseif (is_object($value) && $value instanceof self) {
            $this->value = $value->getValue();
        } else {
            throw new \InvalidArgumentException('Expected double');
        }
    }
}
