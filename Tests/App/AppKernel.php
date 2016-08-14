<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Smartbox\CoreBundle\DependencyInjection\CacheDriversCompilerPass;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Smartbox\CoreBundle\SmartboxCoreBundle(),
        ];

        switch ($this->getEnvironment()) {
            case CacheDriversCompilerPass::PREDEFINED_CACHE_DRIVER_PREDIS:
                $bundles[] = new Snc\RedisBundle\SncRedisBundle();
                break;
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $config = 'config';
        if ($this->getEnvironment() !== 'test') {
            $config = 'config_' . $this->getEnvironment();
        }

        $loader->load($this->getRootDir() . '/config/' . $config . '.yml');
    }
}
