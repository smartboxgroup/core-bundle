<?php

namespace Smartbox\CoreBundle;

use Smartbox\CoreBundle\DependencyInjection\CacheDriversCompilerPass;
use Smartbox\CoreBundle\DependencyInjection\SerializationCacheCompilerPass;
use Smartbox\CoreBundle\DependencyInjection\SmokeTestCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SmartboxCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SmokeTestCompilerPass());
        $container->addCompilerPass(new CacheDriversCompilerPass());
        $container->addCompilerPass(new SerializationCacheCompilerPass());
    }
}
