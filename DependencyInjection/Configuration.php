<?php

namespace Smartbox\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    public const CONFIG_ROOT = 'smartbox_core';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_ROOT);

        $rootNode = $treeBuilder->getRootNode();
        $rootNode
            ->children()
                ->scalarNode('fixtures_path')
                    ->info('Base path to store/lookup the entity fixtures')
                    ->defaultNull()
                ->end()
                ->arrayNode('entities_namespaces')
                    ->info('Namespaces to look for entity classes')
                    ->prototype('scalar')->end()
                ->end()
                ->append($this->getCacheDriversNode())
                ->append($this->getSerializationCacheNode())
            ->end();

        return $treeBuilder;
    }

    protected function getCacheDriversNode()
    {
        $configRoot = self::CONFIG_ROOT;
        $configNode = CacheDriversCompilerPass::CONFIG_NODE;
        $cacheDriverServicePrefix = CacheDriversCompilerPass::CACHE_DRIVER_SERVICE_ID_PREFIX;

        $root = new ArrayNodeDefinition(CacheDriversCompilerPass::CONFIG_NODE);
        $root->info("Configure cache drivers.\n
    1) predis (predefined driver which requires https://github.com/snc/SncRedisBundle and predis library/extension)
        - add packages to composer.json:
            \"snc/redis-bundle\": \"^1.1\"
            \"predis/predis\": \"^1.0\"

        - register bundle in AppKernel.php:
            new Snc\\RedisBundle\\SncRedisBundle(),

        - define \"cache\" client for SncRedisBundle:
            snc_redis:
                clients:
                    cache:
                        type: predis
                        alias: default
                        dsn: redis://localhost
        
        - add configuration to your config.yml:
            {$configRoot}:
                {$configNode}:
                    predis:
                        service: ~
        
        - you can access this driver by service reference @{$cacheDriverServicePrefix}predis
    
    2) null (predefined driver just to simulate the cache - nothing is stored in cache)
        - add configuration to your config.yml:
            {$configRoot}:
                {$configNode}:
                    null:
                        service: ~
        
        - you can access this driver by service reference @{$cacheDriverServicePrefix}null
    
    3) custom driver with any name
        - create your own cache service which implements Smartbox\\\\\Utils\\Cache\\CacheServiceInterface
            class MyCacheService implements Smartbox\\\\\Utils\\Cache\\CacheServiceInterface
            {
                // implement methods
            }
        
        - register service
            my_cache_driver_service_id:
                class: MyCacheService
        
        - add configuration to your config.yml:
            {$configRoot}:
                {$configNode}:
                    my_cache_driver:
                        service: \"@my_cache_driver_service_id\"
        
        - you can access this driver by service reference @{$cacheDriverServicePrefix}my_cache_driver
        "
        )
        ->isRequired()
        ->requiresAtLeastOneElement()
        ->useAttributeAsKey('driver_name')
        ->prototype('array')
            ->children()
                ->scalarNode('service')
                    ->info('Service id for the cache driver (@service_id or just service_id)')
                    ->isRequired()
                ->end()
                ->booleanNode('default')
                    ->info('If any of drivers is marked as default, the first defined driver will be taken. Otherwise the last one marked as default will be used.')
                ->end()
            ->end()
        ->end();

        return $root;
    }

    protected function getSerializationCacheNode()
    {
        $configRoot = self::CONFIG_ROOT;
        $configNode = SerializationCacheCompilerPass::CONFIG_NODE;

        $root = new ArrayNodeDefinition($configNode);
        $root->info('Configure serialization cache')
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('enabled')
                    ->info('Enable or disable serialization cache.')
                    ->defaultValue(false)
                ->end()
                ->arrayNode('cached_visitors')
                    ->prototype('scalar')->end()
                    ->info('The class name of the visitor type you would like the cache enabled for.')
                ->end()
                ->scalarNode('cache_driver')
                    ->info(
                        \sprintf(
                            'Driver name: predis or any other custom driver configured in "%s".',
                            $configRoot.'.'.CacheDriversCompilerPass::CONFIG_NODE
                        )
                    )
                    ->defaultValue(CacheDriversCompilerPass::DEFAULT_CACHE_DRIVER_SERVICE_ID)
                ->end()
            ->end()
        ->end();

        return $root;
    }
}