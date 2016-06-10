<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException;
use Smartbox\CoreBundle\Serializer\ArrayDeserializationVisitor;
use Smartbox\CoreBundle\Serializer\DeserializationTypesValidator;
use Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;

class ArrayDeserializationVisitorFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /** @var SerializerInterface $serializer */
    private $serializer;

    public function setup()
    {
        $builder = new SerializerBuilder();

        /** @var \JMS\Serializer\Construction\ObjectConstructorInterface|\PHPUnit_Framework_MockObject_MockObject $objectConstructor */
        $objectConstructor = $this->getMockBuilder('\JMS\Serializer\Construction\ObjectConstructorInterface')->getMock();

        $this->serializer = $builder
            ->setDeserializationVisitor(
                'array',
                new ArrayDeserializationVisitor(
                    new IdenticalPropertyNamingStrategy(),
                    $objectConstructor,
                    new DeserializationTypesValidator(new StrongDeserializationCastingChecker())
                )
            )
            ->addMetadataDir(__DIR__ . '/../Fixtures/Entity', 'Smartbox\CoreBundle\Tests\Fixtures\Entity')
            ->build()
        ;
    }

    public function testItShouldDeserializeValidEntity()
    {
        $data = [
            'title' => 'some title',
            'description' => 'some description',
            'note' => 'some note',
            'enabled' => true,
        ];

        $obj = $this->serializer->deserialize($data, TestEntity::class, 'array');
        $this->assertEquals('some title', $obj->getTitle());
        $this->assertEquals('some description', $obj->getDescription());
        $this->assertEquals('some note', $obj->getNote());
        $this->assertTrue($obj->isEnabled());
    }

    public function testItShouldDeserializeValidEntityWithVersion()
    {
        $data = [
            'title' => 'some title',
            'description' => 22,
            'note' => 'some note',
        ];

        // description is not valid but it's introduced in V2 of the entity
        // we will deserialize for V1 so the error should not be triggered

        $context = new DeserializationContext();
        $context->setVersion(EntityConstants::VERSION_1);

        $obj = $this->serializer->deserialize($data, TestEntity::class, 'array', $context);
        $this->assertEquals('some title', $obj->getTitle());
        $this->assertNull($obj->getDescription());
        $this->assertEquals('some note', $obj->getNote());
    }

    public function testItShouldDeserializeValidEntityWithGroup()
    {
        $data = [
            'title' => 11,
            'description' => 'some description',
            'note' => 33,
        ];

        // Title and note are not valid valid but they are not available in the group B so the error
        // should not be triggered

        $context = new DeserializationContext();
        $context->setGroups([EntityConstants::GROUP_C]);

        $obj = $this->serializer->deserialize($data, TestEntity::class, 'array', $context);
        $this->assertNull($obj->getTitle());
        $this->assertEquals('some description', $obj->getDescription());
        $this->assertNull($obj->getNote());
    }

    public function testItShouldNotDeserializeAnInvalidEntity()
    {
        $this->setExpectedException(DeserializationTypeMismatchException::class, 'Type mismatch in property "description"');

        $data = [
            'title' => 'some title',
            'description' => [],
            'note' => 'some note',
        ];

        $this->serializer->deserialize($data, TestEntity::class, 'array');
    }
}
