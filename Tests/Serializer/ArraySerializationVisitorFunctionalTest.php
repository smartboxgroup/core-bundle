<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Serializer\ArraySerializationVisitor;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;

class ArraySerializationVisitorFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /** @var SerializerInterface $serializer */
    private $serializer;

    protected function setUp()
    {
        $builder = new SerializerBuilder();

        /** @var \JMS\Serializer\Construction\ObjectConstructorInterface|\PHPUnit_Framework_MockObject_MockObject $objectConstructor */
        $objectConstructor = $this->getMockBuilder('\JMS\Serializer\Construction\ObjectConstructorInterface')
            ->getMock();

        $this->serializer = $builder
            ->setSerializationVisitor(
                'array',
                new ArraySerializationVisitor(
                    new IdenticalPropertyNamingStrategy(),
                    $objectConstructor
                )
            )
            ->addMetadataDir(__DIR__.'/../Fixtures/Entity', 'Smartbox\CoreBundle\Tests\Fixtures\Entity')
            ->build();
    }

    public function testItShouldSerializeValidEntity()
    {
        $data = new TestEntity();
        $data->setTitle('some title');
        $data->setDescription('some description');
        $data->setNote('some note');

        $arrayData = $this->serializer->serialize($data, 'array');
        $this->assertEquals(
            [
                'internalType' => TestEntity::class,            // We are using the IdenticalPropertyNamingStrategy
                'title' => 'some title',
                'description' => 'some description',
                'note' => 'some note',
            ],
            $arrayData
        );
    }

    public function testItShouldSerializeValidEntityWithVersion()
    {
        $data = new TestEntity();
        $data->setTitle('some title');
        $data->setDescription(22);
        $data->setNote('some note');

        // description is not valid but it's introduced in V2 of the entity
        // we will deserialize for V1 so the error should not be triggered

        $context = new SerializationContext();
        $context->setVersion(EntityConstants::VERSION_1);

        $arrayData = $this->serializer->serialize($data, 'array', $context);
        $this->assertEquals(
            [
                'internalType' => TestEntity::class,        // We are using the IdenticalPropertyNamingStrategy
                'title' => 'some title',
                'note' => 'some note',
            ],
            $arrayData
        );
    }

    public function testItShouldSerializeValidEntityWithGroup()
    {
        $data = new TestEntity();
        $data->setTitle(11);
        $data->setDescription('some description');
        $data->setNote(33);

        // Title and note are not valid valid but they are not available in the group B so the error
        // should not be triggered

        $context = new SerializationContext();
        $context->setGroups([EntityConstants::GROUP_C]);

        $arrayData = $this->serializer->serialize($data, 'array', $context);
        $this->assertEquals(
            [
                'description' => 'some description',
            ],
            $arrayData
        );
    }
}
