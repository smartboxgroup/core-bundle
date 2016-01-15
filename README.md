# SmartboxCoreBundle
Core features

## Installation and usage
To install the bundle, you just need to:

1. Add the repository to composer as:
```
    "require": {
        "smartbox/core-bundle": "dev-master"
    },
    "repositories": [
        {
            "type": "vcs",
            "url":  "git@gitlab.production.smartbox.com:smartesb/core-bundle.git"
        }
    ],
```

2. Add it to your AppKernel.php file
    new Smartbox\CoreBundle\SmartboxCoreBundle(),

4. Configure bundle:
```
  php console.php config:dump-reference smartbox_core
  
\# Default configuration for extension with alias: "smartbox_core"
smartbox_core:

    # Base path to store/lookup the entity fixtures
    fixtures_path:        null

    # Namespaces to look for entity classes
    entities_namespaces:  []

    # Configure serialization cache.
    #
    #     Add configuration to your config.yml:
    #         smartbox_core:
    #             serialization_cache:
    #                 enabled: true
    #                 cache_driver: [driver_name]
    #
    serialization_cache:

        # Enable or disable serialization cache.
        enabled:              false

        # Driver name: predis or custom.
        #
        #     Add configuration for specific driver:
        #         1) predis (requires https://github.com/snc/SncRedisBundle and predis library/extension)
        #             - add packages to composer.json:
        #                 "snc/redis-bundle": "^1.1"
        #                 "predis/predis": "^1.0"
        #
        #             - register bundle in AppKernel.php:
        #                 new Snc\RedisBundle\SncRedisBundle(),
        #
        #             - define "cache" client for SncRedisBundle:
        #                 snc_redis:
        #                     clients:
        #                         cache:
        #                             type: predis
        #                             alias: default
        #                             dsn: redis://localhost
        #
        #         2) custom
        #             - create your own cache service which implements Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface
        #                 class MyCacheService implements Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface
        #                 {
        #                     // implement methods
        #                 }
        #
        #             - register service with the name "smartcore.cache_service"
        #                 smartcore.cache_service:
        #                     class: MyCacheService
        #
        #
        cache_driver:         predis # One of "predis"; "custom"
```

## Contributing
1. Fork it!
2. Create your feature branch: `git checkout -b my-new-feature`
3. Commit your changes: `git commit -am 'Add some feature'`
4. Push to the branch: `git push origin my-new-feature`
5. Submit a pull request :D

## Tests

Check out the small test app within Tests/Fixtures/app

## History

## Contributors
Jose Rufino, Marcin Skurski, Luciano Mammino, Alberto Rodrigo

