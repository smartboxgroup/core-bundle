<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\NonStringCastableTypeException;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Smartbox\CoreBundle\Serializer\DeserializationTypesValidator;
use Smartbox\CoreBundle\Serializer\JsonDeserializationVisitor;
use Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;
use Smartbox\CoreBundle\Type\Entity;

/**
 * @coversDefaultClass \Smartbox\CoreBundle\Serializer\JsonDeserializationVisitor
 * @group json
 */
class JsonDeserializationVisitorFunctionalTest extends TestCase
{
    /** @var SerializerInterface */
    private $serializer;

    protected function setUp(): void
    {
        $builder = new SerializerBuilder();

        $this->serializer = $builder
            ->setDeserializationVisitor(
                'json',
                new JsonDeserializationVisitor(
                    new DeserializationTypesValidator(new StrongDeserializationCastingChecker()),
                    new \JMS\Serializer\JsonDeserializationVisitor()
                )
            )
            ->addMetadataDir(__DIR__.'/../Fixtures/Entity', Entity::class)
            ->build();
    }

    protected function tearDown(): void
    {
        $this->serializer = null;
    }

    public function testItShouldDeserializeValidEntity(): void
    {
        $data =
            '{
            "title": "some title",
            "description": "some description",
            "note": "some note",
            "enabled": true
        }';

        $obj = $this->serializer->deserialize($data, TestEntity::class, 'json');
        $this->assertEquals('some title', $obj->getTitle());
        $this->assertEquals('some description', $obj->getDescription());
        $this->assertEquals('some note', $obj->getNote());
        $this->assertTrue($obj->isEnabled());
    }

    public function testItShouldDeserializeValidEntityWithVersion(): void
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

        $obj = $this->serializer->deserialize(
            $data,
            TestEntity::class,
            'json',
            $context
        );
        $this->assertEquals('some title', $obj->getTitle());
        $this->assertNull($obj->getDescription());
        $this->assertEquals('some note', $obj->getNote());
        $this->assertFalse($obj->isEnabled());
    }

    public function testItShouldDeserializeValidEntityWithGroup(): void
    {
        $data =
            '{
            "title": 11,
            "description": "some description",
            "note": 33,
            "enabled": true
        }';

        // Title and note are not valid, but they are not available in the group B so the error
        // should not be triggered

        $context = new DeserializationContext();
        $context->setGroups([EntityConstants::GROUP_C]);

        $obj = $this->serializer->deserialize(
            $data,
            TestEntity::class,
            'json',
            $context
        );
        $this->assertNull($obj->getTitle());
        $this->assertEquals('some description', $obj->getDescription());
        $this->assertNull($obj->getNote());
        $this->assertFalse($obj->isEnabled());
    }

    public function testItShouldNotDeserializeAnInvalidEntity(): void
    {
        $this->expectException(NonStringCastableTypeException::class);
        $this->expectExceptionMessage('Cannot convert value of type "array" to string');

        $data =
            '{
            "title": "some title",
            "description": {},
            "note": "some note",
            "enabled": true
        }';

        $this->serializer->deserialize($data, TestEntity::class, 'json');
    }
}