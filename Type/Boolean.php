<?php
namespace Smartbox\CoreBundle\Type;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Boolean
 * @package Smartbox\CoreBundle\Type
 */
class Boolean extends Basic
{
    /**
     * @Assert\Type(type="boolean")
     * @JMS\Type("boolean")
     * @JMS\Expose
     * @JMS\Groups({"logs"})
     * @var boolean
     */
    protected $value;

    /**
     * @param bool $value
     */
    public function __construct($value = false)
    {
        $this->setValue($value);
    }

    /**
     * @return bool
     */
    public function getValue()
    {
        return (bool)$this->value;
    }

    /***
     * @param bool $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value)
    {
        if ((is_bool($value))) {
            $this->value = (bool)$value;
        } elseif (is_object($value) && $value instanceof Boolean) {
            $this->value = $value->getValue();
        } else {
            throw new \InvalidArgumentException("Expected boolean");
        }
    }
}
