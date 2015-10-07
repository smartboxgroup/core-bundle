<?php
namespace Smartbox\CoreBundle\Type;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class String
 * @package Smartbox\CoreBundle\Type
 */
class String extends Basic
{
    /**
     * @Assert\Type(type="string")
     * @JMS\Type("string")
     * @JMS\Groups({"logs"})
     * @var string
     */
    protected $value = "";

    /***
     * @param string $value
     */
    public function __construct($value = "")
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return (string)$this->value;
    }

    /***
     * @param string $value
     * @throws \InvalidArgumentException
     */
    public function setValue($value)
    {
        if (is_string($value)) {
            $this->value = $value;
        } elseif (is_object($value) && $value instanceof String) {
            $this->value = $value->getValue();
        } else {
            throw new \InvalidArgumentException("Expected string");
        }
    }

}