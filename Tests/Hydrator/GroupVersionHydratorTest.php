<?php

namespace Smartbox\CoreBundle\Tests\Hydrator;

use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\StaticPropertyMetadata;
use Metadata\MetadataFactory;
use PHPUnit\Framework\MockObject\MockObject;
use Smartbox\CoreBundle\Hydrator\GroupVersionHydrator;
use Smartbox\CoreBundle\Tests\BaseKernelTestCase;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestNestedEntity;
use Smartbox\CoreBundle\Type\Entity;

/**
 * @group hydrator
 */
class GroupVersionHydratorTest extends BaseKernelTestCase
{
    public const GROUP = 'testGroup';
    public const VERSION = 'v17';

    /**
     * @var GroupVersionHydrator
     */
    private GroupVersionHydrator $hydrator;

    /**
     * @var MockObject|ClassMetadata
     */
    private MockObject $metadataClass;
    private array $propertiesMetadata;

    protected function setUp(): void
    {
        parent::setUp();

        $testNestedEntity = new TestNestedEntity();
        $testNestedEntity->setItem(new TestEntity());

        $version = $this->createMock(StaticPropertyMetadata::class);
        $version->class = TestEntity::class;
        $version->groups = ['metadata'];
        $version->serializedName = '_apiVersion';
        $version->name = 'version';
        $version->type = [
            'name' => 'string',
            'params' => [],
        ];
        $version->method('getValue')->willReturn(self::VERSION);

        $internalType = $this->createMock(StaticPropertyMetadata::class);
        $internalType->class = Entity::class;
        $internalType->serializedName = '_type';
        $internalType->groups = ['metadata'];
        $internalType->name = 'internalType';
        $internalType->type = [
            'name' => 'string',
            'params' => [],
        ];
        $internalType->method('getValue')->willReturn(self::GROUP);

        $entityGroup = $this->createMock(StaticPropertyMetadata::class);
        $entityGroup->name = 'entityGroup';
        $entityGroup->class = Entity::class;
        $entityGroup->groups = ['metadata'];
        $entityGroup->type = [
            'name' => 'string',
            'params' => [],
        ];
        $entityGroup->serializedName = '_group';
        $entityGroup->method('getValue')->willReturn(self::GROUP);

        $title = $this->createMock(StaticPropertyMetadata::class);
        $title->name = 'title';
        $title->class = TestEntity::class;
        $title->type = [
            'name' => 'string',
            'params' => [],
        ];
        $title->method('getValue')->willReturn($title->name);

        $description = $this->createMock(StaticPropertyMetadata::class);
        $description->name = 'description';
        $description->class = TestEntity::class;
        $description->type = [
            'name' => 'string',
            'params' => [],
        ];
        $description->method('getValue')->willReturn($description->name);

        $note = $this->createMock(StaticPropertyMetadata::class);
        $note->name = 'note';
        $note->class = TestEntity::class;
        $note->type = [
            'name' => 'string',
            'params' => [],
        ];
        $note->method('getValue')->willReturn($note->name);

        $enabled = $this->createMock(StaticPropertyMetadata::class);
        $enabled->name = 'enabled';
        $enabled->class = TestEntity::class;
        $enabled->type = [
            'name' => 'string',
            'params' => [],
        ];
        $enabled->method('getValue')->willReturn($enabled->name);

        $this->propertiesMetadata = [
            'internalType' => $internalType,
            'entityGroup' => $entityGroup,
            'version' => $version,
            'title' => $title,
            'description' => $description,
            'note' => $note,
            'enabled' => $enabled,
        ];

        $this->metadataClass = $this->createMock(ClassMetadata::class);
        $this->metadataClass->name = TestEntity::class;
        $this->metadataClass->propertyMetadata = $this->propertiesMetadata;

        $metadataFactory = $this->createMock(MetadataFactory::class);
        $metadataFactory->method('getMetadataForClass')->willReturn($this->metadataClass);

        $this->hydrator = new GroupVersionHydrator($metadataFactory);
    }

    public function testItShouldHydrateAnEntity(): void
    {
        $entity = new TestEntity();
        $this->hydrator->hydrate($entity, self::GROUP, self::VERSION);

        $this->assertEquals(self::GROUP, $entity->getEntityGroup(), 'The group was not set correctly');
        $this->assertEquals(self::VERSION, $entity->getAPIVersion(), 'The version was not set correctly');
    }

    public function testItShouldHydrateAnArrayOfEntities(): void
    {
        $array = [
            new TestEntity(),
            new TestEntity(),
        ];

        $this->hydrator->hydrate($array, self::GROUP, self::VERSION);
        foreach ($array as $index => $entity) {
            $this->assertEquals(
                self::GROUP,
                $entity->getEntityGroup(),
                'The group was not set correctly on entity #'.$index
            );
            $this->assertEquals(
                self::VERSION,
                $entity->getAPIVersion(),
                'The version was not set correctly on entity #'.$index
            );
        }
    }

    public function testItShouldHydrateAnEntityWithNestedEntities(): void
    {
        $nestedEntity = new TestNestedEntity();

        $entity = new TestEntity();
        $entity->setEntityGroup(self::GROUP);
        $entity->setAPIVersion(self::VERSION);

        $genericItems = [
            $entity,
            $entity,
        ];

        $assocItems = [
            'key1' => $entity,
            'key2' => $entity,
        ];

        $nestedEntity->setItem($entity);
        $nestedEntity->setGenericItems($genericItems);
        $nestedEntity->setAssocItems($assocItems);

        $item = $this->createMock(StaticPropertyMetadata::class);
        $item->class = TestNestedEntity::class;
        $item->name = 'item';
        $item->groups = ['A'];
        $item->type = [
            'name' => TestEntity::class,
            'params' => [],
        ];
        $item->entityGroup = self::GROUP;
        $item->version = self::VERSION;
        $item->method('getValue')->willReturn($entity);

        $genericItem = $this->createMock(StaticPropertyMetadata::class);
        $genericItem->class = TestNestedEntity::class;
        $genericItem->name = 'genericItem';
        $genericItem->groups = ['A'];
        $genericItem->type = [
            'name' => Entity::class,
            'params' => [],
        ];
        $genericItem->method('getValue')->willReturn($genericItem->name);

        $items = $this->createMock(StaticPropertyMetadata::class);
        $items->class = TestNestedEntity::class;
        $items->groups = ['A'];
        $items->type = [
            'name' => 'array',
            'params' => [
                'name' => TestEntity::class,
                'params' => [],
            ],
        ];
        $items->method('getValue')->willReturn($nestedEntity);

        $assocItemsMetadata = $this->createMock(StaticPropertyMetadata::class);
        $assocItemsMetadata->class = TestNestedEntity::class;
        $assocItemsMetadata->groups = ['A'];
        $assocItemsMetadata->type = [
            'name' => 'array',
            'params' => [
                [
                    'name' => 'string',
                    'params' => [],
                ],
                [
                    'name' => TestEntity::class,
                    'params' => [],
                ]
            ],
        ];
        $assocItemsMetadata->method('getValue')->willReturn($nestedEntity);

        $genericItemsMetadata = $this->createMock(StaticPropertyMetadata::class);
        $genericItemsMetadata->class = TestNestedEntity::class;
        $genericItemsMetadata->groups = ['A'];
        $genericItemsMetadata->type = [
            'name' => 'array',
            'params' => [
                'name' => Entity::class,
                'params' => [],
            ],
        ];
        $genericItemsMetadata->method('getValue')->willReturn($nestedEntity);

        $propertiesNestedEntity = $this->propertiesMetadata;
        unset(
            $propertiesNestedEntity['title'],
            $propertiesNestedEntity['description'],
            $propertiesNestedEntity['note'],
            $propertiesNestedEntity['enabled']
        );

        $propertiesNestedEntity['item'] = $item;
        $propertiesNestedEntity['genericItem'] = $genericItem;
        $propertiesNestedEntity['items'] = $items;
        $propertiesNestedEntity['assocItems'] = $assocItemsMetadata;
        $propertiesNestedEntity['genericItems'] = $genericItemsMetadata;

        $metadataNestedEntity = $this->createMock(ClassMetadata::class);
        $metadataNestedEntity->propertyMetadata = $propertiesNestedEntity;

        $metadataFactory = $this->createMock(MetadataFactory::class);
        $metadataFactory->method('getMetadataForClass')->will(
            $this->onConsecutiveCalls(
                $metadataNestedEntity,
                $this->metadataClass,
                $metadataNestedEntity
            )
        );

        $this->hydrator = new GroupVersionHydrator($metadataFactory);

        $this->hydrator->hydrate($nestedEntity, self::GROUP, self::VERSION);

        //test simple sub-nestedEntity
        $this->assertEquals(
            self::GROUP,
            $nestedEntity->getItem()->getEntityGroup(),
            'The group was not set correctly on the simple sub-nestedEntity'
        );
        $this->assertEquals(
            self::VERSION,
            $nestedEntity->getItem()->getAPIVersion(),
            'The version was not set correctly on the simple sub-nestedEntity'
        );

        //test sub collection of entities
        foreach ($nestedEntity->getGenericItems() as $index => $subEntity) {
            $this->assertEquals(
                self::GROUP,
                $subEntity->getEntityGroup(),
                'The group was not set correctly on the sub-collection at index #'.$index
            );
            $this->assertEquals(
                self::VERSION,
                $subEntity->getAPIVersion(),
                'The version was not set correctly on the sub-collection at index #'.$index
            );
        }

        //test sub collection (associative) of entities
        foreach ($nestedEntity->getAssocItems() as $key => $subEntity) {
            $this->assertEquals(
                self::GROUP,
                $subEntity->getEntityGroup(),
                'The group was not set correctly on the sub-collection (associative) at item with key '.$key
            );
            $this->assertEquals(
                self::VERSION,
                $subEntity->getAPIVersion(),
                'The version was not set correctly on the sub-collection (associative) at item with key '.$key
            );
        }
    }
}