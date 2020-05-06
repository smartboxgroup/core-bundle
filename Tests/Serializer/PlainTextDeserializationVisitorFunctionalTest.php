<?php

namespace Smartbox\CoreBundle\Tests\Serializer;

use JMS\Serializer\Naming\IdenticalPropertyNamingStrategy;
use JMS\Serializer\SerializerBuilder;
use Smartbox\CoreBundle\Serializer\PlainTextDeserializationVisitor;

class PlainTextDeserializationVisitorFunctionalTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Deserializer should return the same text it received, without modifications.
     */
    public function testDeserialization()
    {
        $builder = new SerializerBuilder();

        $serializer = $builder
            ->setDeserializationVisitor(
                'plain_text',
                new PlainTextDeserializationVisitor(new IdenticalPropertyNamingStrategy())
            )
            ->build();

        $data = 'I am a silly API that returns responses in plain text';

        $result = $serializer->deserialize($data, 'string', 'plain_text');

        $this->assertSame($data, $result, 'Visitor modified the payload, it should have kept it intact.');
    }
}
