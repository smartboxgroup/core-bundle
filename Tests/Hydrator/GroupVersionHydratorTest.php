<?php

namespace Smartbox\CoreBundle\Tests\Hydrator;

use Smartbox\CoreBundle\Hydrator\GroupVersionHydrator;
use Smartbox\CoreBundle\Tests\BaseKernelTestCase;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestNestedEntity;

class GroupVersionHydratorTest extends BaseKernelTestCase
{
    const GROUP = 'testGroup';
    const VERSION = 'v17';

    /**
     * @var GroupVersionHydrator
     */
    private $hydrator;

    public function setup()
    {
        parent::setup();
        $metadataFactory = $this->getContainer()->get('serializer')->getMetadataFactory();
        $this->hydrator = new GroupVersionHydrator($metadataFactory);
    }

    public function testItShouldHydrateAnEntity()
    {
        $entity = new TestEntity();
        $this->hydrator->hydrate($entity, self::GROUP, self::VERSION);

        $this->assertEquals(self::GROUP, $entity->getEntityGroup(), 'The group was not set correctly');
        $this->assertEquals(self::VERSION, $entity->getAPIVersion(), 'The version was not set correctly');
    }

    public function testItShouldHydrateAnArrayOfEntities()
    {
        /** @var TestEntity[] $array */
        $array = [
            new TestEntity(),
            new TestEntity()
        ];

        $this->hydrator->hydrate($array, self::GROUP, self::VERSION);
        foreach($array as $index => $entity) {
            $this->assertEquals(self::GROUP, $entity->getEntityGroup(), 'The group was not set correctly on entity #'. $index);
            $this->assertEquals(self::VERSION, $entity->getAPIVersion(), 'The version was not set correctly on entity #'. $index);
        }
    }

    public function testItShouldHydrateAnEntityWithNestedEntities()
    {
        $entity = new TestNestedEntity();
        $entity->setItem(new TestEntity());
        $entity->setGenericItems([
            new TestEntity(),
            new TestEntity()
        ]);
        $entity->setAssocItems([
            'key1' => new TestEntity(),
            'key2' => new TestEntity()
        ]);

        $this->hydrator->hydrate($entity, self::GROUP, self::VERSION);

        //test simple sub-entity
        $this->assertEquals(self::GROUP, $entity->getItem()->getEntityGroup(), 'The group was not set correctly on the simple sub-entity');
        $this->assertEquals(self::VERSION, $entity->getItem()->getAPIVersion(), 'The version was not set correctly on the simple sub-entity');

        //test sub collection of entities
        foreach($entity->getGenericItems() as $index => $subEntity) {
            $this->assertEquals(self::GROUP, $subEntity->getEntityGroup(), 'The group was not set correctly on the sub-collection at index #'. $index);
            $this->assertEquals(self::VERSION, $subEntity->getAPIVersion(), 'The version was not set correctly on the sub-collection at index #'. $index);
        }

        //test sub collection (associative) of entities
        foreach($entity->getAssocItems() as $key => $subEntity) {
            $this->assertEquals(self::GROUP, $subEntity->getEntityGroup(), 'The group was not set correctly on the sub-collection (associative) at item with key '. $key);
            $this->assertEquals(self::VERSION, $subEntity->getAPIVersion(), 'The version was not set correctly on the sub-collection (associative) at item with key '. $key);
        }
    }
}
