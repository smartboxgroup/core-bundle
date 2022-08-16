<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Entity;

use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Smartbox\CoreBundle\Type\Entity;

class TestNestedEntity extends Entity
{
    /**
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity
     * @JMS\Groups({EntityConstants::GROUP_A})
     * @JMS\Type("Smartbox\\Tests\Fixtures\Entity\TestEntity")
     * @JMS\Expose
     */
    protected $item;

    /**
     * @var Entity
     * @JMS\Groups({EntityConstants::GROUP_A})
     * @JMS\Type("Smartbox\\Type\Entity")
     * @JMS\Expose
     */
    protected $genericItem;

    /**
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity
     * @JMS\Groups({EntityConstants::GROUP_A})
     * @JMS\Type("array<Smartbox\\Tests\Fixtures\Entity\TestEntity>")
     * @JMS\Expose
     */
    protected $items;

    /**
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity
     * @JMS\Groups({EntityConstants::GROUP_A})
     * @JMS\Type("array<string,Smartbox\\Tests\Fixtures\Entity\TestEntity>")
     * @JMS\Expose
     */
    protected $assocItems;

    /**
     * @var Entity
     * @JMS\Groups({EntityConstants::GROUP_A})
     * @JMS\Type("array<Smartbox\\Type\Entity>")
     * @JMS\Expose
     */
    protected $genericItems;

    /**
     * @return \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     * @param \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     * @return Entity
     */
    public function getGenericItem()
    {
        return $this->genericItem;
    }

    /**
     * @param Entity $genericItem
     */
    public function setGenericItem($genericItem)
    {
        $this->genericItem = $genericItem;
    }

    /**
     * @return \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity[]
     */
    public function getAssocItems()
    {
        return $this->assocItems;
    }

    /**
     * @param \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity[] $assocItems
     */
    public function setAssocItems($assocItems)
    {
        $this->assocItems = $assocItems;
    }

    /**
     * @return Entity[]
     */
    public function getGenericItems()
    {
        return $this->genericItems;
    }

    /**
     * @param Entity[] $genericItems
     */
    public function setGenericItems($genericItems)
    {
        $this->genericItems = $genericItems;
    }
}
