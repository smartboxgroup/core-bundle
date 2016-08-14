<?php

namespace Smartbox\CoreBundle\DependencyInjection;

use Smartbox\CoreBundle\Serializer\Cache\CacheEventsSubscriber;
use Smartbox\CoreBundle\Serializer\Handler\CachedObjectHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class SerializationCacheCompilerPass.
 */
class SerializationCacheCompilerPass implements CompilerPassInterface
{
    const CONFIG_NODE = 'serialization_cache';

    /** @var  ContainerBuilder */
    protected $container;

    public function process(ContainerBuilder $container)
    {
        /** @var SmartboxCoreExtension $extension */
        $extension = $container->getExtension('smartbox_core');
        $config = $extension->getConfig();
        if ($config[self::CONFIG_NODE]['enabled']) {
            $cacheDriverName = $config[self::CONFIG_NODE]['cache_driver'];
            $cacheDriverServiceId = CacheDriversCompilerPass::CACHE_DRIVER_SERVICE_ID_PREFIX.$cacheDriverName;

            if (!$container->hasDefinition($cacheDriverServiceId)) {
                throw new \RuntimeException(
                    sprintf(
                        'Cache driver "%s" configured in "%s" was not found. Configure it by adding "%s" to your configuration.',
                        $cacheDriverName,
                        Configuration::CONFIG_ROOT.'.'.self::CONFIG_NODE.'.cache_driver',
                        Configuration::CONFIG_ROOT.'.'.self::CONFIG_NODE.'.'.$cacheDriverName
                    )
                );
            }

            $cacheDriverServiceDef = $container->getDefinition($cacheDriverServiceId);

            // Serialization cache subscriber
            $serializationCacheSubscriber = $container->setDefinition(
                'smartcore.serializer.subscriber.cache',
                new Definition(CacheEventsSubscriber::class)
            );
            $serializationCacheSubscriber->addMethodCall('setCacheService', [$cacheDriverServiceDef]);
            $serializationCacheSubscriber->addTag('jms_serializer.event_subscriber');

            // Serialization cache handler
            $serializationCacheHandler = $container->setDefinition(
                'smartcore.serializer.handler.cache',
                new Definition(CachedObjectHandler::class)
            );
            $serializationCacheHandler->addMethodCall('setCacheService', [$cacheDriverServiceDef]);
            $serializationCacheHandler->addTag('jms_serializer.subscribing_handler');
        }
    }
}
