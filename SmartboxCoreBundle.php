<?php

namespace Smartbox\CoreBundle;

use Smartbox\CoreBundle\DependencyInjection\SerializationCacheCompilerPass;
use Smartbox\CoreBundle\DependencyInjection\SmokeTestCompilerPass;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SmartboxCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SmokeTestCompilerPass());
        $container->addCompilerPass(new SerializationCacheCompilerPass());
    }
}
