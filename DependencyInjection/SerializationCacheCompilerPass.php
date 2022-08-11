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
    public const CONFIG_NODE = 'serialization_cache';

    /** @var ContainerBuilder */
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
                    \sprintf(
                        'Cache driver "%s" configured in "%s" was not found. Configure it by adding "%s" to your ' .
                        ' configuration.',
                        $cacheDriverName,
                        Configuration::CONFIG_ROOT.'.'.self::CONFIG_NODE.'.cache_driver',
                        Configuration::CONFIG_ROOT.'.'.self::CONFIG_NODE.'.'.$cacheDriverName
                    )
                );
            }

            $cacheDriverServiceDef = $container->getDefinition($cacheDriverServiceId);

            // Test that the Visitor classes in the config exist
            $visitorClasses = $config[self::CONFIG_NODE]['cached_visitors'];
            foreach ($visitorClasses as $class) {
                if (!\class_exists($class)) {
                    throw new \Exception(
                        "The class '$class' configured in smartbox_core.serialization_cache.cached_visitors does " .
                         "not exist."
                    );
                }
                if (!\property_exists($class, CacheEventsSubscriber::DATA_PROPERTY)) {
                    throw new \Exception(
                        "The class '$class' configured in smartbox_core.serialization_cache.cached_visitors does not " .
                        "have the data property and can not be cached."
                    );
                }
            }
            $public = $container->getParameter('kernel.debug');

            // Serialization cache subscriber
            $serializationCacheSubscriber = $container->register(
                'smartcore.serializer.subscriber.cache',
                CacheEventsSubscriber::class
            );
            $serializationCacheSubscriber->setArguments([$visitorClasses]);
            $serializationCacheSubscriber->addMethodCall('setCacheService', [$cacheDriverServiceDef]);
            $serializationCacheSubscriber->addTag('jms_serializer.event_subscriber');
            $serializationCacheSubscriber->setPublic($public);

            // Serialization cache handler
            $serializationCacheHandler = $container->register(
                'smartcore.serializer.handler.cache',
                CachedObjectHandler::class
            );
            $serializationCacheHandler->addMethodCall('setCacheService', [$cacheDriverServiceDef]);
            $serializationCacheHandler->addTag('jms_serializer.subscribing_handler');
            $serializationCacheHandler->setPublic($public);
        }
    }
}