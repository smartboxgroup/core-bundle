<?php

namespace Smartbox\CoreBundle\Tests\Serializer\Handler;

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Smartbox\CoreBundle\Serializer\Handler\CachedObjectHandler;
use JMS\Serializer\SerializationContext;
use Smartbox\CoreBundle\Tests\App\AppKernel;
use Smartbox\CoreBundle\Tests\BaseKernelTestCase;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\CacheableEntity;
use Smartbox\CoreBundle\Tests\Fixtures\Entity\SerializableThing;
use Smartbox\CoreBundle\Tests\App\Utils\Cache\FakeCacheService;
use Smartbox\CoreBundle\Tests\App\Utils\Cache\FakeCacheServiceSpy;
use Smartbox\CoreBundle\Type\Date;
use Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface;

/**
 * @group cache
 */
class CachedObjectHandlerTest extends BaseKernelTestCase
{
    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    private function prepareKernel($env = null): void
    {
        $options = ['environment' => $env, 'enabled' => true];
        self::bootKernel($options);

        self::$kernel->getBundle('SmartboxCoreBundle')->getContainerExtension()->getConfig()['serialization_cache']['enabled'] = true;
    }

    protected function createCacheableEntity($title): CacheableEntity
    {
        $testEntity = new CacheableEntity();
        $testEntity->setTitle($title);
        $testEntity->setEntityGroup('test_group');
        $testEntity->setAPIVersion('1.0');

        return $testEntity;
    }

    public function dataProviderForSerializationFormatWithCache(): array
    {
        return [
            ['json', 'custom'],
            ['json', 'predis'],
        ];
    }

    /**
     * @group test
     * @dataProvider dataProviderForSerializationFormatWithCache
     *
     * @param $format
     * @param $cacheDriver
     */
    public function testSerializationWithCache($format, $cacheDriver): void
    {
        $this->prepareKernel($cacheDriver);

        $cacheServiceSpy = new FakeCacheServiceSpy();

        /** @var CacheServiceInterface|MockObject $cacheServiceMock */
        $cacheServiceMock = $this->getMockBuilder(FakeCacheService::class)
            ->setConstructorArgs([$cacheServiceSpy])
            ->getMock();

        self::$kernel->getContainer()->get('smartcore.serializer.subscriber.cache')->setCacheService($cacheServiceMock);
        self::$kernel->getContainer()->get('smartcore.serializer.handler.cache')->setCacheService($cacheServiceMock);

        /** @var SerializerInterface $serializer */
        $serializer = self::$kernel->getContainer()->get('jms_serializer');
        $cacheData = $this->createCacheableEntity('title 1');
        $cacheDataArray = [
            '_type' => CacheableEntity::class,
            'title' => 'title 1',
            'entityGroup' => 'test_group',
            'version' => '1.0',
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

//        $kernel->shutdown();
    }

    public function dataProviderForSerializationFormatWithoutCache(): array
    {
        return [
            ['json'],
            ['xml'],
        ];
    }

    /**
     * @dataProvider dataProviderForSerializationFormatWithoutCache
     *
     * @param $format
     */
    public function testSerializationWithoutCacheForXML($format): void
    {
        $kernel = $this->prepareKernel();

        /** @var SerializerInterface $serializer */
        $serializer = self::$kernel->getContainer()->get('jms_serializer');
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