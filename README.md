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
```

    \# Default configuration for extension with alias: "smartbox_core"

```
  smartbox_core:

      # Base path to store/lookup the entity fixtures
      fixtures_path:        null

      # Namespaces to look for entity classes
      entities_namespaces:  []

      # Configure cache drivers.
      #
      #     1) predis (predefined driver which requires https://github.com/snc/SncRedisBundle and predis library/extension)
      #         - add packages to composer.json:
      #             "snc/redis-bundle": "^1.1"
      #             "predis/predis": "^1.0"
      #
      #         - register bundle in AppKernel.php:
      #             new Snc\RedisBundle\SncRedisBundle(),
      #
      #         - define "cache" client for SncRedisBundle:
      #             snc_redis:
      #                 clients:
      #                     cache:
      #                         type: predis
      #                         alias: default
      #                         dsn: redis://localhost
      #
      #         - add configuration to your config.yml:
      #             smartbox_core:
      #                 cache_drivers:
      #                     predis:
      #                         service: ~
      #
      #         - you can access this driver by service reference @smartcore.cache_driver.predis
      #
      #     2) null (predefined driver just to simulate the cache - nothing is stored in cache)
      #         - add configuration to your config.yml:
      #             smartbox_core:
      #                 cache_drivers:
      #                     null:
      #                         service: ~
      #
      #         - you can access this driver by service reference @smartcore.cache_driver.null
      #
      #     3) custom driver with any name
      #         - create your own cache service which implements Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface
      #             class MyCacheService implements Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface
      #             {
      #                 // implement methods
      #             }
      #
      #         - register service
      #             my_cache_driver_service_id:
      #                 class: MyCacheService
      #
      #         - add configuration to your config.yml:
      #             smartbox_core:
      #                 cache_drivers:
      #                     my_cache_driver:
      #                         service: "@my_cache_driver_service_id"
      #
      #         - you can access this driver by service reference @smartcore.cache_driver.my_cache_driver
      #
      cache_drivers:        # Required

          # Prototype
          driver_name:

              # Service id for the cache driver (@service_id or just service_id)
              service:              ~ # Required

              # If any of drivers is marked as default, the first defined driver will be taken. Otherwise the last one marked as default will be used.
              default:              ~

      # Configure serialization cache
      serialization_cache:

          # Enable or disable serialization cache.
          enabled:              false

          # Driver name: predis or any other custom driver configured in "smartbox_core.cache_drivers".
          cache_driver:         smartcore.cache_service

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
Jose Rufino, Marcin Skurski, Luciano Mammino, Alberto Rodrigo, David Camprubi, Arthur Thevenet

