<?php

namespace Smartbox\CoreBundle\Type;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Date.
 */
class Date extends Basic
{
    /**
     * @Assert\DateTime()
     * @JMS\Type("DateTime")
     * @JMS\Expose
     * @JMS\Groups({"logs"})
     *
     * @var \DateTime
     */
    protected $value;

    /**
     * @param \DateTime $value
     */
    public function __construct(\DateTime $value = null)
    {
        $this->setValue($value);
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }
}
