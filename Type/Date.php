<?php

namespace Smartbox\CoreBundle\Type;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Date
 * @package Smartbox\CoreBundle\Type
 */
class Date extends Basic
{
    /**
     * @Assert\DateTime()
     * @JMS\Type("DateTime")
     * @JMS\Expose
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
