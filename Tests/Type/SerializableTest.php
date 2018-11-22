<?php

namespace Smartbox\CoreBundle\Tests\Entity;

use JMS\Serializer\Serializer;
use Smartbox\CoreBundle\Type\Date;
use Smartbox\CoreBundle\Type\SerializableArray;
use Smartbox\CoreBundle\Type\IntegerType;
use Smartbox\CoreBundle\Type\StringType;
use Smartbox\CoreBundle\Type\Entity;
use Smartbox\CoreBundle\Type\SerializableInterface;
use Smartbox\CoreBundle\Tests\BaseKernelTestCase;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\SerializableThing;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;

/**
 * Class EntityTest.
 */
class SerializableTest extends BaseKernelTestCase
{
    public function validAndInvalidStringsDataProvider()
    {
        $exceptClass = 'InvalidArgumentException';

        return [
            ['xxx', null],
            [null, null],
            ['', null],
            [-1, $exceptClass],
            [234, $exceptClass],
            [12.32, $exceptClass],
        ];
    }

    public function objectsToSerializeProvider()
    {
        $arrEntityA = new SerializableArray();
        $arrEntityA->set('AAAA', new StringType('XXXXXXX'));

        $arrEntity = new SerializableArray();
        $arrEntity->set('response', $arrEntityA);
        $arrEntity->set('response2', $arrEntityA);
        $arrEntity->set('number', new IntegerType(2));
        $arrEntity->set('string', new StringType('Lorem ipsum'));
        $arrEntity->set('other', $arrEntityA);

        return [
            [$arrEntity],
        ];
    }

    /**
     * @dataProvider objectsToSerializeProvider
     */
    public function testSerializationEntity(SerializableInterface $object)
    {
        /** @var Serializer $serializer */
        $serializer = $this->getContainer()->get('jms_serializer');

        $json = $serializer->serialize($object, 'json');
        $entityAfterJson = $serializer->deserialize($json, Entity::class, 'json');
        $this->assertEquals($object, $entityAfterJson);

        $xml = $serializer->serialize($object, 'xml');
        $entityAfterXml = $serializer->deserialize($xml, Entity::class, 'xml');
        $this->assertEquals($object, $entityAfterXml);
    }

    public function serializableObjectsToSerialize()
    {
        $thing = new SerializableThing();
        $thing->setStringValue('foo');
        $thing->setIntegerValue(17);
        $thing->setDoubleValue(17.17);
        $thing->setArrayOfDates([
            new Date(),
        ]);
        $thing->setArrayOfEntities([
            new TestEntity(),
        ]);

        return [
            [$thing],
        ];
    }

    /**
     * @dataProvider serializableObjectsToSerialize
     */
    public function testSerializationSerializable(SerializableInterface $serializable)
    {
        /** @var Serializer $serializer */
        $serializer = $this->getContainer()->get('jms_serializer');

        $json = $serializer->serialize($serializable, 'json');

        $entityAfterJson = $serializer->deserialize($json, SerializableInterface::class, 'json');
        $this->assertEquals($serializable, $entityAfterJson);

        $xml = $serializer->serialize($serializable, 'xml');
        $entityAfterXml = $serializer->deserialize($xml, SerializableInterface::class, 'xml');
        $this->assertEquals($serializable, $entityAfterXml);
    }
}
