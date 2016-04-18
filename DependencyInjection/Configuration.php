<?php

namespace Smartbox\CoreBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('smartbox_core');
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
                ->arrayNode('serialization_cache')
                    ->info("Configure serialization cache.\n
    Add configuration to your config.yml:
        smartbox_core:
            serialization_cache:
                enabled: true
                cache_driver: [driver_name]
                    ")
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enable or disable serialization cache.')
                            ->defaultValue(false)
                        ->end()
                        ->enumNode('cache_driver')
                            ->info("Driver name: predis or custom.\n
    Add configuration for specific driver:
        1) predis (requires https://github.com/snc/SncRedisBundle and predis library/extension)
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

        2) custom
            - create your own cache service which implements Smartbox\\CoreBundle\\Utils\\Cache\\CacheServiceInterface
                class MyCacheService implements Smartbox\\CoreBundle\\Utils\\Cache\\CacheServiceInterface
                {
                    // implement methods
                }

            - register service with the name \"smartcore.cache_service\"
                smartcore.cache_service:
                    class: MyCacheService

                            ")
                            ->values(array('predis', 'custom'))
                            ->defaultValue('predis')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
