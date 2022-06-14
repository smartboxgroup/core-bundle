<?php

namespace Smartbox\CoreBundle\Tests;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Smartbox\CoreBundle\DependencyInjection\CacheDriversCompilerPass;

class AppKernel extends Kernel
{
    private $cacheDir;

    public function registerBundles(): array
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new \Symfony\Bundle\MonologBundle\MonologBundle(),
            new \JMS\SerializerBundle\JMSSerializerBundle(),
            new \Smartbox\CoreBundle\SmartboxCoreBundle(),
        ];

        switch ($this->getEnvironment()) {
            case CacheDriversCompilerPass::PREDEFINED_CACHE_DRIVER_PREDIS:
                $bundles[] = new \Snc\RedisBundle\SncRedisBundle();
                break;
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $config = 'config';
        if ('test' !== $this->getEnvironment()) {
            $config = 'config_'.$this->getEnvironment();
        }

        $loader->load($this->getRootDir().'/config/'.$config.'.yml');
    }

    public function getCacheDir(): string
    {
        if (!$this->cacheDir) {
            $this->cacheDir = sys_get_temp_dir().'/sbx_core_bundle_tests';
        }

        return $this->cacheDir;
    }

    public function shutdown()
    {
        parent::shutdown();

        $fs = new Filesystem();
        $fs->remove($this->getCacheDir());
    }
}