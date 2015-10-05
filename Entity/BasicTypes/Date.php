<?php

namespace Smartbox\CoreBundle\Entity\BasicTypes;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Date
 * @package Smartbox\CoreBundle\Entity\BasicTypes
 */
class Date extends Basic
{
    /**
     * @Assert\DateTime()
     * @JMS\Type("DateTime")
     * @JMS\Groups({"logs"})
     * @var \DateTime
     */
    protected $value = null;

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
