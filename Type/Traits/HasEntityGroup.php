<?php

namespace Smartbox\CoreBundle\Type\Traits;

use JMS\Serializer\Annotation as JMS;

trait HasEntityGroup
{
    /**
     * @JMS\Type("string")
     * @JMS\Expose
     * @JMS\SerializedName("_group")
     * @JMS\Groups({"metadata"})
     *
     * @var string
     */
    protected string $entityGroup;

    /**
     * @return string
     */
    public function getEntityGroup(): string
    {
        return $this->entityGroup;
    }

    /**
     * @param string $group
     */
    public function setEntityGroup(string $group): void
    {
        if (!empty($group) && !\is_string($group)) {
            throw new \InvalidArgumentException('Expected null or string in method setGroup');
        }

        $this->entityGroup = $group;
    }
}