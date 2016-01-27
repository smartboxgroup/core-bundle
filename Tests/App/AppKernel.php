<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Smartbox\CoreBundle\DependencyInjection\SerializationCacheCompilerPass;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Smartbox\CoreBundle\SmartboxCoreBundle(),
        );

        switch ($this->getEnvironment()) {
            case SerializationCacheCompilerPass::CACHE_SERVICE_DRIVER_PREDIS :
                $bundles[] = new Snc\RedisBundle\SncRedisBundle();
                break;
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $config = 'config';
        if (in_array($this->getEnvironment(), SerializationCacheCompilerPass::getSupportedDrivers())) {
            $config = 'config_' . $this->getEnvironment();
        }

        $loader->load($this->getRootDir().'/config/'.$config.'.yml');
    }
}