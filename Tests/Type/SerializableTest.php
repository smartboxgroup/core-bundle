<?php

namespace Smartbox\CoreBundle\Tests\Entity;

use JMS\Serializer\Serializer;
use Smartbox\CoreBundle\Type\Date;
use Smartbox\CoreBundle\Type\SerializableArray;
use Smartbox\CoreBundle\Type\Integer;
use Smartbox\CoreBundle\Type\String;
use Smartbox\CoreBundle\Type\Entity;
use Smartbox\CoreBundle\Type\EntityInterface;
use Smartbox\CoreBundle\Type\SerializableInterface;
use Smartbox\CoreBundle\Tests\BaseKernelTestCase;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\SerializableThing;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\TestEntity;

/**
 * Class EntityTest
 * @package Smartbox\CoreBundle\Tests\Entity
 */
class SerializableTest extends BaseKernelTestCase
{
    public function validAndInvalidStringsDataProvider()
    {
        $exceptClass = 'InvalidArgumentException';

        return array(
            array('xxx', null),
            array(null, null),
            array("", null),
            array(-1, $exceptClass),
            array(234, $exceptClass),
            array(12.32, $exceptClass)
        );
    }

    public function objectsToSerializeProvider()
    {
        $arrEntityA = new SerializableArray();
        $arrEntityA->set('AAAA', new String("XXXXXXX"));

        $arrEntity = new SerializableArray();
        $arrEntity->set('response', $arrEntityA);
        $arrEntity->set('response2', $arrEntityA);
        $arrEntity->set('number', new Integer(2));
        $arrEntity->set('string', new String("Lorem ipsum"));
        $arrEntity->set('other', $arrEntityA);

        return array(
            array($arrEntity)
        );
    }

    /**
     * @dataProvider objectsToSerializeProvider
     */
    public function testSerializationEntity(SerializableInterface $object)
    {
        /** @var Serializer $serializer */
        $serializer = $this->getContainer()->get('serializer');

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
            new Date()
        ]);
        $thing->setArrayOfEntities([
            new TestEntity()
        ]);

        return [
            [$thing]
        ];
    }

    /**
     * @dataProvider serializableObjectsToSerialize
     */
    public function testSerializationSerializable(SerializableInterface $serializable)
    {
        /** @var Serializer $serializer */
        $serializer = $this->getContainer()->get('serializer');

        $json = $serializer->serialize($serializable, 'json');

        $entityAfterJson = $serializer->deserialize($json, SerializableInterface::class, 'json');
        $this->assertEquals($serializable, $entityAfterJson);


        $xml = $serializer->serialize($serializable, 'xml');
        $entityAfterXml = $serializer->deserialize($xml, SerializableInterface::class, 'xml');
        $this->assertEquals($serializable, $entityAfterXml);
    }
}
