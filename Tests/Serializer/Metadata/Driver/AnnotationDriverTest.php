<?php

namespace Smartbox\CoreBundle\Tests\Serializer\Metadata\Driver;

use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Tests\AppKernel;
use Smartbox\CoreBundle\Tests\Fixtures\Serializables\SerializableWithExclusionPolicyAll;
use Smartbox\CoreBundle\Tests\Fixtures\Serializables\SerializableWithExclusionPolicyAllAndFewPropertiesExposed;
use Smartbox\CoreBundle\Tests\Fixtures\Serializables\SerializableWithExclusionPolicyNone;
use Smartbox\CoreBundle\Tests\Fixtures\Serializables\SerializableWithExclusionPolicyNoneAndFewPropertiesExcluded;
use Smartbox\CoreBundle\Tests\Fixtures\Serializables\SerializableWithoutExclusionPolicy;
use Smartbox\CoreBundle\Type\SerializableInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AnnotationDriverTest extends KernelTestCase
{
    /**
     * @var SerializerInterface
     */
    protected $serializer;

    public static function getKernelClass()
    {
        return AppKernel::class;
    }

    protected function setUp(): void
    {
       static::bootKernel();

        $this->serializer = self::$kernel->getContainer()->get('jms_serializer');
    }

    /**
     * @return array
     */
    public function dataProviderForSerializables()
    {
        $data = [];

        // should serialize only "_type"
        $object1 = new SerializableWithoutExclusionPolicy();
        $object1->setIntegerValue(4);
        $object1->setDoubleValue(0.08);
        $object1->setStringValue('test 1');
        $data[] = [$object1, ['_type' => SerializableWithoutExclusionPolicy::class]];

        // should be the same as SerializableWithoutExclusionPolicy
        $object2 = new SerializableWithExclusionPolicyAll();
        $object2->setIntegerValue(4);
        $object2->setDoubleValue(0.08);
        $object2->setStringValue('test 2');
        $data[] = [$object2, ['_type' => SerializableWithExclusionPolicyAll::class]];

        // should serialize everything
        $object3 = new SerializableWithExclusionPolicyNone();
        $object3->setIntegerValue(5);
        $object3->setDoubleValue(0.03);
        $object3->setStringValue('test 3');
        $data[] = [
            $object3,
            [
                '_type' => SerializableWithExclusionPolicyNone::class,
                'integer_value' => 5,
                'double_value' => 0.03,
                'string_value' => 'test 3',
            ],
        ];

        // should serialize only EXPOSED properties
        $object4 = new SerializableWithExclusionPolicyAllAndFewPropertiesExposed();
        $object4->setIntegerValue(5);
        $object4->setDoubleValue(0.03);
        $object4->setStringValue('test 4');
        $data[] = [
            $object4,
            [
                '_type' => SerializableWithExclusionPolicyAllAndFewPropertiesExposed::class,
                'integer_value' => 5,
                'string_value' => 'test 4',
            ],
        ];

        // should serialize only NOT EXCLUDED properties
        $object5 = new SerializableWithExclusionPolicyNoneAndFewPropertiesExcluded();
        $object5->setIntegerValue(5);
        $object5->setDoubleValue(0.03);
        $object5->setStringValue('test 5');
        $data[] = [
            $object5,
            [
                '_type' => SerializableWithExclusionPolicyNoneAndFewPropertiesExcluded::class,
                'double_value' => 0.03,
            ],
        ];

        return $data;
    }

    /**
     * @dataProvider dataProviderForSerializables
     *
     * @param SerializableInterface $data
     * @param array                 $expectedSerializedData
     */
    public function testSerialization(SerializableInterface $data, array $expectedSerializedData)
    {
        $serializedData = $this->serializer->serialize($data, 'array');

        $this->assertEquals($expectedSerializedData, $serializedData);
    }
}