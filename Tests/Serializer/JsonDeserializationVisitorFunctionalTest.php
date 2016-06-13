<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Serializer\DeserializationTypesValidator;
use Smartbox\CoreBundle\Serializer\JsonDeserializationVisitor;
use Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;

class JsonDeserializationVisitorFunctionalTest extends \PHPUnit_Framework_TestCase
{
    /** @var SerializerInterface */
    private $serializer;

    protected function setUp()
    {
        $builder = new SerializerBuilder();

        /** @var \JMS\Serializer\Construction\ObjectConstructorInterface|\PHPUnit_Framework_MockObject_MockObject $objectConstructor */
        $objectConstructor = $this->getMockBuilder('\JMS\Serializer\Construction\ObjectConstructorInterface')->getMock();

        $this->serializer = $builder
            ->setDeserializationVisitor(
                'json',
                new JsonDeserializationVisitor(
                    new IdenticalPropertyNamingStrategy(),
                    $objectConstructor,
                    new DeserializationTypesValidator(new StrongDeserializationCastingChecker())
                )
            )
            ->addMetadataDir(__DIR__ . '/../Fixtures/Entity', 'Smartbox\CoreBundle\Tests\Fixtures\Entity')
            ->build()
        ;
    }

    protected function tearDown()
    {
        $this->serializer = null;
    }

    public function testItShouldDeserializeValidEntity()
    {
        $data =
        '{
            "title": "some title",
            "description": "some description",
            "note": "some note",
            "enabled": true
        }';

        $obj = $this->serializer->deserialize($data, 'Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity', 'json');
        $this->assertEquals('some title', $obj->getTitle());
        $this->assertEquals('some description', $obj->getDescription());
        $this->assertEquals('some note', $obj->getNote());
        $this->assertTrue($obj->isEnabled());
    }

    public function testItShouldDeserializeValidEntityWithVersion()
    {
        $data =
        '{
            "title": "some title",
            "description": 22,
            "note": "some note",
            "enabled": false
        }';

        // description is not valid but it's introduced in V2 of the entity, we will deserialize for V1 so the error
        // should not be triggered

        $context = new DeserializationContext();
        $context->setVersion(EntityConstants::VERSION_1);

        $obj = $this->serializer->deserialize($data, 'Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity', 'json', $context);
        $this->assertEquals('some title', $obj->getTitle());
        $this->assertNull($obj->getDescription());
        $this->assertEquals('some note', $obj->getNote());
        $this->assertFalse($obj->isEnabled());
    }

    public function testItShouldDeserializeValidEntityWithGroup()
    {
        $data =
        '{
            "title": 11,
            "description": "some description",
            "note": 33,
            "enabled": true
        }';

        // Title and note are not valid valid but they are not available in the group B so the error
        // should not be triggered

        $context = new DeserializationContext();
        $context->setGroups([EntityConstants::GROUP_C]);

        $obj = $this->serializer->deserialize($data, 'Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity', 'json', $context);
        $this->assertNull($obj->getTitle());
        $this->assertEquals('some description', $obj->getDescription());
        $this->assertNull($obj->getNote());
        $this->assertFalse($obj->isEnabled());
    }

    /**
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     * @expectedExceptionMessage Type mismatch in property "description"
     */
    public function testItShouldNotDeserializeAnInvalidEntity()
    {
        $data =
        '{
            "title": "some title",
            "description": {},
            "note": "some note",
            "enabled": true
        }';

        $this->serializer->deserialize($data, 'Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity', 'json');
    }
}
