<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Smartbox\CoreBundle\Entity\Entity;

class TestEntity extends Entity{

    const GROUP_A = 'A';
    const GROUP_B = 'B';

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @JMS\Groups({"A"})
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @JMS\Groups({"A","B"})
     * @JMS\Since("v2")
     * @JMS\Type("string")
     */
    protected $description;

    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @JMS\Type("string")
     */
    protected $note;
}