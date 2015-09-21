<?php

namespace Smartbox\CoreBundle\Tests\Fixtures\Entity;


use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Smartbox\CoreBundle\Entity\Entity;

class TestNestedEntity extends Entity {

    /**
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity
     * @JMS\Groups({"A"})
     * @JMS\Type("Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity")
     */
    protected $item;

    /**
     * @var Entity
     * @JMS\Groups({"A"})
     * @JMS\Type("Smartbox\CoreBundle\Entity\Entity")
     */
    protected $generic_item;

    /**
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity
     * @JMS\Groups({"A"})
     * @JMS\Type("array<Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity>")
     */
    protected $items;

    /**
     * @var \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity
     * @JMS\Groups({"A"})
     * @JMS\Type("array<string,Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity>")
     */
    protected $assoc_items;

    /**
     * @var Entity
     * @JMS\Groups({"A"})
     * @JMS\Type("array<Smartbox\CoreBundle\Entity\Entity>")
     */
    protected $generic_items;

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
        return $this->generic_item;
    }

    /**
     * @param Entity $generic_item
     */
    public function setGenericItem($generic_item)
    {
        $this->generic_item = $generic_item;
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
        return $this->assoc_items;
    }

    /**
     * @param \Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity[] $assoc_items
     */
    public function setAssocItems($assoc_items)
    {
        $this->assoc_items = $assoc_items;
    }

    /**
     * @return Entity[]
     */
    public function getGenericItems()
    {
        return $this->generic_items;
    }

    /**
     * @param Entity[] $generic_items
     */
    public function setGenericItems($generic_items)
    {
        $this->generic_items = $generic_items;
    }

    
}