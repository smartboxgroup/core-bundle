<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Serializer\DeserializationTypesValidator;
use Smartbox\CoreBundle\Serializer\StrongDeserializationCastingChecker;
use Smartbox\CoreBundle\Serializer\XmlDeserializationVisitor;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\EntityConstants;
use JMS\Serializer\XmlDeserializationVisitor as JMSXmlDeserializationVisitor;

class XmlDeserializationVisitorFunctionalTest extends \PHPUnit\Framework\TestCase
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
            ->setDeserializationVisitor(
                'xml',
                new XmlDeserializationVisitor(
                    new IdenticalPropertyNamingStrategy(),
                    $objectConstructor,
                    new DeserializationTypesValidator(new StrongDeserializationCastingChecker()),
                    new JMSXmlDeserializationVisitor(true, [])
                )
            )
            ->addMetadataDir(__DIR__.'/../Fixtures/Entity', 'Smartbox\CoreBundle\Tests\Fixtures\Entity')
            ->build();
    }

    public function testItShouldDeserializeValidEntity()
    {
        $data =
            '<?xml version="1.0" encoding="UTF-8"?>
            <result>
              <title><![CDATA[some title]]></title>
              <description><![CDATA[some description]]></description>
              <note><![CDATA[some note]]></note>
            </result>';

        $obj = $this->serializer->deserialize($data, 'Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity', 'xml');
        $this->assertEquals('some title', $obj->getTitle());
        $this->assertEquals('some description', $obj->getDescription());
        $this->assertEquals('some note', $obj->getNote());
    }

    public function testItShouldDeserializeValidEntityWithVersion()
    {
        $data =
            '<?xml version="1.0" encoding="UTF-8"?>
            <result>
              <title><![CDATA[some title]]></title>
              <description><entry><![CDATA[some wrong data]]></entry></description>
              <note><![CDATA[some note]]></note>
            </result>';

        // description is not valid but it's introduced in V2 of the entity, we will deserialize for V1 so the error
        // should not be triggered

        $context = new DeserializationContext();
        $context->setVersion(EntityConstants::VERSION_1);

        $obj = $this->serializer->deserialize(
            $data,
            'Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity',
            'xml',
            $context
        );
        $this->assertEquals('some title', $obj->getTitle());
        $this->assertNull($obj->getDescription());
        $this->assertEquals('some note', $obj->getNote());
    }

    public function testItShouldDeserializeValidEntityWithGroup()
    {
        $data =
            '<?xml version="1.0" encoding="UTF-8"?>
            <result>
              <title><![CDATA[11]]></title>
              <description><![CDATA[some description]]></description>
              <note><![CDATA[33]]></note>
            </result>';

        // Title and note are not valid valid but they are not available in the group B so the error
        // should not be triggered

        $context = new DeserializationContext();
        $context->setGroups([EntityConstants::GROUP_C]);

        $obj = $this->serializer->deserialize(
            $data,
            'Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity',
            'xml',
            $context
        );
        $this->assertNull($obj->getTitle());
        $this->assertEquals('some description', $obj->getDescription());
        $this->assertNull($obj->getNote());
    }

    /**
     * @expectedException \Smartbox\CoreBundle\Exception\Serializer\DeserializationTypeMismatchException
     * @expectedExceptionMessage Type mismatch in property "description"
     */
    public function testItShouldNotDeserializeAnInvalidEntity()
    {
        $data =
            '<?xml version="1.0" encoding="UTF-8"?>
            <result>
              <title><![CDATA[some title]]></title>
              <description><something><![CDATA[2]]></something></description>
              <note><![CDATA[some note]]></note>
            </result>';

        $this->serializer->deserialize($data, 'Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity', 'xml');
    }
}
