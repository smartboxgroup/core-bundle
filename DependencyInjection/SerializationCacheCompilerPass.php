<?php

namespace Smartbox\CoreBundle\DependencyInjection;

use Smartbox\CoreBundle\Serializer\Cache\CacheEventsSubscriber;
use Smartbox\CoreBundle\Serializer\Handler\CachedObjectHandler;
use Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface;
use Smartbox\CoreBundle\Utils\Cache\PredisCacheService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SerializationCacheCompilerPass.
 */
class SerializationCacheCompilerPass implements CompilerPassInterface
{
    const CACHE_SERVICE_ID = 'smartcore.cache_service';

    const CACHE_SERVICE_DRIVER_PREDIS = 'predis';
    const CACHE_SERVICE_DRIVER_CUSTOM = 'custom';

    /** @var  ContainerBuilder */
    protected $container;

    public function process(ContainerBuilder $container)
    {
        /** @var SmartboxCoreExtension $extension */
        $extension = $container->getExtension('smartbox_core');
        $config = $extension->getConfig();
        if ($config['serialization_cache']['enabled']) {
            $driverOption = $config['serialization_cache']['cache_driver'];

            switch ($driverOption) {
                case self::CACHE_SERVICE_DRIVER_PREDIS:
                    $cacheServiceDef = new Definition(PredisCacheService::class, [new Reference('snc_redis.cache')]);
                    $container->setDefinition(
                        self::CACHE_SERVICE_ID,
                        $cacheServiceDef
                    );
                    break;
                case self::CACHE_SERVICE_DRIVER_CUSTOM:
                    if (!$container->hasDefinition(self::CACHE_SERVICE_ID)) {
                        throw new \RuntimeException(
                            sprintf(
                                'If you want to use "%s" driver you have to define service with id "%s" which implements interface "%s"',
                                $driverOption,
                                self::CACHE_SERVICE_ID,
                                CacheServiceInterface::class
                            )
                        );
                    } else {
                        $cacheServiceDefinition = $container->getDefinition(self::CACHE_SERVICE_ID);
                        $reflection = new \ReflectionClass($cacheServiceDefinition->getClass());
                        if (!$reflection->implementsInterface(CacheServiceInterface::class)) {
                            throw new \RuntimeException(
                                sprintf(
                                    'Cache service with id "%s" should implement interface "%s"',
                                    self::CACHE_SERVICE_ID,
                                    CacheServiceInterface::class
                                )
                            );
                        }
                    }
                    break;
                default:
                    throw new \RuntimeException(
                        sprintf(
                            'Cache service driver with name "%s" is not supported. Supported drivers: [%s]',
                            $driverOption,
                            implode(', ', self::getSupportedDrivers())
                        )
                    );
            }

            $cacheServiceReference = new Reference(self::CACHE_SERVICE_ID);

            // Serialization cache subscriber
            $serializationCacheSubscriber = $container->setDefinition(
                'smartcore.serializer.subscriber.cache',
                new Definition(CacheEventsSubscriber::class)
            );
            $serializationCacheSubscriber->addMethodCall('setCacheService', [$cacheServiceReference]);
            $serializationCacheSubscriber->addTag('jms_serializer.event_subscriber');

            // Serialization cache handler
            $serializationCacheHandler = $container->setDefinition(
                'smartcore.serializer.handler.cache',
                new Definition(CachedObjectHandler::class)
            );
            $serializationCacheHandler->addMethodCall('setCacheService', [$cacheServiceReference]);
            $serializationCacheHandler->addTag('jms_serializer.subscribing_handler');
        }
    }

    public static function getSupportedDrivers()
    {
        return [
            self::CACHE_SERVICE_DRIVER_PREDIS,
            self::CACHE_SERVICE_DRIVER_CUSTOM,
        ];
    }
}
