<?php
namespace Smartbox\CoreBundle\Entity\Traits;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

trait HasGroup
{
    /**
     * @JMS\Type("string")
     * @JMS\Groups({"metadata"})
     * @var  string
     */
    protected $group;

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        if (!empty($group) && !is_string($group)) {
            throw new \InvalidArgumentException("Expected null or string in method setGroup");
        }

        $this->group = $group;
    }
}