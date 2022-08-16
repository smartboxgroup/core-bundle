<?php

namespace Smartbox\CoreBundle\Tests\App;

use JMS\SerializerBundle\JMSSerializerBundle;
use Smartbox\CoreBundle\DependencyInjection\CacheDriversCompilerPass;
use Smartbox\CoreBundle\SmartboxCoreBundle;
use Snc\RedisBundle\SncRedisBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    private $cacheDir;

    public function registerBundles(): array
    {
        $bundles = [
            new FrameworkBundle(),
            new MonologBundle(),
            new JMSSerializerBundle(),
            new SmartboxCoreBundle(),
        ];

        if ($this->getEnvironment() === CacheDriversCompilerPass::PREDEFINED_CACHE_DRIVER_PREDIS) {
            $bundles[] = new SncRedisBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $config = 'config';
        if ('test' !== $this->getEnvironment()) {
            $config = 'config_'.$this->getEnvironment();
        }

        $loader->load('Tests/App/config/'.$config.'.yml');
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