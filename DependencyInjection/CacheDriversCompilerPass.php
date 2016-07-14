<?php

namespace Smartbox\CoreBundle\DependencyInjection;

use Smartbox\CoreBundle\Utils\Cache\CacheServiceInterface;
use Smartbox\CoreBundle\Utils\Cache\NullCacheService;
use Smartbox\CoreBundle\Utils\Cache\PredisCacheService;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class CacheDriversCompilerPass.
 */
class CacheDriversCompilerPass implements CompilerPassInterface
{
    const CONFIG_NODE = 'cache_drivers';
    const CACHE_DRIVER_SERVICE_ID_PREFIX = 'smartcore.cache_driver.';
    const DEFAULT_CACHE_DRIVER_SERVICE_ID = 'smartcore.cache_service';
    const PREDEFINED_CACHE_DRIVER_SERVICE_ID_PREFIX = 'smartcore.predefined_cache_driver.';
    const PREDEFINED_CACHE_DRIVER_PREDIS = 'predis';
    const PREDEFINED_CACHE_DRIVER_NULL = 'predefined_null';

    /** @var  ContainerBuilder */
    protected $container;

    protected $predefinedCacheDrivers = [self::PREDEFINED_CACHE_DRIVER_PREDIS];

    public function process(ContainerBuilder $container)
    {
        $this->container = $container;

        /** @var SmartboxCoreExtension $extension */
        $extension = $container->getExtension(Configuration::CONFIG_ROOT);
        $config = $extension->getConfig();

        if (isset($config[self::CONFIG_NODE]) && !empty($config[self::CONFIG_NODE])) {
            $cacheDrivers = $config[self::CONFIG_NODE];
            $defaultCacheDriver = null;
            
            foreach ($cacheDrivers as $cacheDriverName => $cacheDriverConf) {
                $cacheDriverServiceId = $this->getCacheDriverServiceId($cacheDriverName, $cacheDriverConf);

                // check if service exists
                if (!$container->hasDefinition($cacheDriverServiceId)) {
                    throw new \RuntimeException(
                        sprintf(
                            'Service "%s" defined for the cache driver configuration "%s" doesn\'t exist.',
                            '@' . $cacheDriverServiceId,
                            Configuration::CONFIG_ROOT . '.' . self::CONFIG_NODE . '.' . $cacheDriverName
                        )
                    );
                }

                $cacheDriverDef = $container->getDefinition($cacheDriverServiceId);

                // check if service implements required interface
                $cacheDriverReflection = new \ReflectionClass($cacheDriverDef->getClass());
                if (!$cacheDriverReflection->implementsInterface(CacheServiceInterface::class)) {
                    throw new \RuntimeException(
                        sprintf(
                            'Service "%s" defined for the cache driver configuration "%s" should implement "%s" interface.',
                            '@' . $cacheDriverServiceId,
                            Configuration::CONFIG_ROOT . '.' . self::CONFIG_NODE . '.' . $cacheDriverName,
                            CacheServiceInterface::class
                        )
                    );
                }

                $container->setDefinition(
                    self::CACHE_DRIVER_SERVICE_ID_PREFIX . $cacheDriverName,
                    $cacheDriverDef
                );

                // there should be always one default cache driver
                if (is_null($defaultCacheDriver) || (isset($cacheDriverConf['default']) && $cacheDriverConf['default'])) {
                    $defaultCacheDriver = self::CACHE_DRIVER_SERVICE_ID_PREFIX . $cacheDriverName;
                }
            }

            // create alias for default cache driver
            $container->setAlias(self::DEFAULT_CACHE_DRIVER_SERVICE_ID, $defaultCacheDriver);
        }
    }

    /**
     * Method returns cache driver service id.
     * If configured cache driver is predefined (it means that configuration for predefined driver is null, eq: predis: ~)
     * we register the service in the container and return its id.
     * If cache driver is custom, we check if this service exists in the container and return its name.
     *
     * @param $cacheDriverName
     * @param array $cacheDriverConf
     * @return string
     */
    protected function getCacheDriverServiceId($cacheDriverName, array $cacheDriverConf)
    {
        $cacheDriverServiceId = null;

        if (in_array($cacheDriverName, $this->getPredefinedCacheDriversNames()) && is_null($cacheDriverConf['service'])) {
            // is predefined driver
            switch($cacheDriverName) {
                case self::PREDEFINED_CACHE_DRIVER_NULL: // register predefined cache driver - null
                    $cacheDriverServiceId = self::PREDEFINED_CACHE_DRIVER_SERVICE_ID_PREFIX . $cacheDriverName;

                    $cacheDriverServiceDef = new Definition(NullCacheService::class);
                    $this->container->setDefinition(
                        $cacheDriverServiceId,
                        $cacheDriverServiceDef
                    );
                    break;
                case self::PREDEFINED_CACHE_DRIVER_PREDIS: // register predefined cache driver - predis
                    $cacheDriverServiceId = self::PREDEFINED_CACHE_DRIVER_SERVICE_ID_PREFIX . $cacheDriverName;

                    $cacheDriverServiceDef = new Definition(PredisCacheService::class, [new Reference('snc_redis.cache')]);
                    $this->container->setDefinition(
                        $cacheDriverServiceId,
                        $cacheDriverServiceDef
                    );
                    break;
            }
        } else {
            $cacheDriverServiceId = ltrim($cacheDriverConf['service'], '@');
        }

        return $cacheDriverServiceId;
    }

    /**
     * @return array
     */
    protected function getPredefinedCacheDriversNames()
    {
        return [
            self::PREDEFINED_CACHE_DRIVER_PREDIS,
            self::PREDEFINED_CACHE_DRIVER_NULL
        ];
    }
}
