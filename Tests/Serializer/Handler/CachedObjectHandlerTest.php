<?php

namespace Smartbox\CoreBundle\Tests\Serializer\Handler;

use JMS\Serializer\SerializerInterface;
use Smartbox\CoreBundle\Serializer\Handler\CachedObjectHandler;
use JMS\Serializer\SerializationContext;
use Smartbox\CoreBundle\Tests\AppKernel;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\CacheableEntity;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\SerializableThing;
use Smartbox\CoreBundle\Tests\Utils\Cache\FakeCacheService;
use Smartbox\CoreBundle\Tests\Utils\Cache\FakeCacheServiceSpy;
use Smartbox\CoreBundle\Type\Date;
use Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CachedObjectHandlerTest extends KernelTestCase
{
    protected static function getKernelClass()
    {
        return AppKernel::class;
    }

    private function prepareKernel($env = null)
    {
        $options = ['environment' => $env];
        $kernel = static::createKernel($options);
        $kernel->boot();

        return $kernel;
    }

    protected function createCacheableEntity($title)
    {
        $testEntity = new CacheableEntity();
        $testEntity->setTitle($title);

        return $testEntity;
    }

    public function dataProviderForSerializationFormatWithCache()
    {
        return [
            ['json', 'custom'],
            ['array', 'custom'],
            ['mongo_array', 'custom'],
            ['json', 'predis'],
            ['array', 'predis'],
            ['mongo_array', 'predis'],
        ];
    }

    /**
     * @dataProvider dataProviderForSerializationFormatWithCache
     *
     * @param $format
     * @param $cacheDriver
     */
    public function testSerializationWithCache($format, $cacheDriver)
    {
        $kernel = $this->prepareKernel($cacheDriver);
        $container = $kernel->getContainer();

        $cacheServiceSpy = new FakeCacheServiceSpy();

        /** @var CacheServiceInterface|\PHPUnit_Framework_MockObject_MockObject $cacheServiceMock */
        $cacheServiceMock = $this->getMockBuilder(FakeCacheService::class)
            ->setConstructorArgs([$cacheServiceSpy])
            ->setMethods(null)
            ->getMock();

        $container->get('smartcore.serializer.subscriber.cache')->setCacheService($cacheServiceMock);
        $container->get('smartcore.serializer.handler.cache')->setCacheService($cacheServiceMock);

        /** @var SerializerInterface $serializer */
        $serializer = $container->get('jms_serializer');
        $cacheData = $this->createCacheableEntity('title 1');
        $cacheDataArray = [
            '_type' => 'Smartbox\\CoreBundle\\Tests\\Fixtures\\Entity\\CacheableEntity',
            'title' => 'title 1',
        ];

        $entity = new SerializableThing();
        $entity->setIntegerValue(10);
        $entity->setStringValue('test');
        $entity->setDoubleValue(17.17);
        $entity->setArrayOfDates(
            [
                new Date(),
                new Date(),
            ]
        );
        $entity->setNestedEntity(clone $cacheData);
        $entity->setArrayOfEntities(
            [
                clone $cacheData,
                clone $cacheData,
            ]
        );

        $context = SerializationContext::create();

        $serializedEntity = $serializer->serialize($entity, $format, $context);
        $cacheKey = CachedObjectHandler::getDataCacheKey($cacheData, $context);
        $deserializedEntity = $serializer->deserialize($serializedEntity, SerializerInterface::class, $format);

        $this->assertEquals($entity, $deserializedEntity);

        $expectedSpyLog = [];
        if (\in_array($format, ['json', 'array'])) {
            $expectedSpyLog = [
                [
                    'method' => 'exists',
                    'arguments' => [$cacheKey],
                    'result' => false,
                ],
                [
                    'method' => 'set',
                    'arguments' => [$cacheKey, $cacheDataArray, null],
                    'result' => true,
                ],
                [
                    'method' => 'exists',
                    'arguments' => [$cacheKey],
                    'result' => true,
                ],
                [
                    'method' => 'get',
                    'arguments' => [$cacheKey],
                    'result' => $cacheDataArray,
                ],
                [
                    'method' => 'exists',
                    'arguments' => [$cacheKey],
                    'result' => true,
                ],
                [
                    'method' => 'get',
                    'arguments' => [$cacheKey],
                    'result' => $cacheDataArray,
                ],
            ];
        }
        $this->assertEquals(
            $expectedSpyLog,
            $cacheServiceSpy->getLog(),
            'Methods of cache service were not executed with proper order or arguments.'
        );

        $kernel->shutdown();
    }

    public function dataProviderForSerializationFormatWithoutCache()
    {
        return [
            ['json'],
            ['array'],
            ['mongo_array'],
            ['xml'],
        ];
    }

    /**
     * @dataProvider dataProviderForSerializationFormatWithoutCache
     *
     * @param $format
     */
    public function testSerializationWithoutCacheForXML($format)
    {
        $kernel = $this->prepareKernel();
        $container = $kernel->getContainer();

        /** @var SerializerInterface $serializer */
        $serializer = $container->get('jms_serializer');
        $cacheData = $this->createCacheableEntity('title 1');

        $entity = new SerializableThing();
        $entity->setIntegerValue(10);
        $entity->setStringValue('test');
        $entity->setDoubleValue(17.17);
        $entity->setArrayOfDates(
            [
                new Date(),
                new Date(),
            ]
        );
        $entity->setNestedEntity(clone $cacheData);
        $entity->setArrayOfEntities(
            [
                clone $cacheData,
                clone $cacheData,
            ]
        );

        $context = new SerializationContext();

        $serializedEntity = $serializer->serialize($entity, $format, $context);
        $deserializedEntity = $serializer->deserialize($serializedEntity, SerializerInterface::class, $format);

        $this->assertEquals($entity, $deserializedEntity);
        $kernel->shutdown();
    }
}
