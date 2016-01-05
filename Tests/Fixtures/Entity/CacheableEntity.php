<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Entity;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Serializer\Cache\SerializerCacheableInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Smartbox\CoreBundle\Type\Entity;

class CacheableEntity extends Entity implements SerializerCacheableInterface
{
    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @JMS\Groups({EntityConstants::GROUP_DEFAULT, EntityConstants::GROUP_A})
     * @JMS\Type("string")
     * @JMS\Expose
     */
    protected $title;

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
}
