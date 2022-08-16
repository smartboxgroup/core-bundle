<?php

namespace Smartbox\CoreBundle\Tests;

use Smartbox\CoreBundle\DependencyInjection\SerializationCacheCompilerPass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BaseKernelTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
    }

    protected function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new SerializationCacheCompilerPass());
    }
}