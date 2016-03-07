<?php

namespace Smartbox\CoreBundle\DependencyInjection;

use Smartbox\CoreBundle\Utils\SmokeTest\Generic\ConnectivityCheckSmokeTest;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SmokeTestCompilerPass
 *
 * @package Smartbox\CoreBundle\DependencyInjection
 */
class SmokeTestCompilerPass implements CompilerPassInterface
{
    /** @var ContainerBuilder */
    protected $container;

    public function process(ContainerBuilder $container)
    {
        $smokeTestCommand = $container->getDefinition('smartcore.command.smoke_test');

        $serviceIds = $container->findTaggedServiceIds('smartbox.smoke_test');
        foreach ($serviceIds as $serviceId => $tags) {
            $smokeTestCommand->addMethodCall('addTest', [$serviceId, new Reference($serviceId)]);
        }

        // initialize smartcore.smoke_test.generic.connectivity_check with items which needs to check connectivity
        $connectivityCheckSmokeTestDef = $container->getDefinition('smartcore.smoke_test.generic.connectivity_check');
        $connectivityCheckSmokeTestItems = $container->findTaggedServiceIds(ConnectivityCheckSmokeTest::TAG_ITEM);
        foreach ($connectivityCheckSmokeTestItems as $serviceName => $tags) {
            $connectivityCheckSmokeTestDef->addMethodCall('addItem', array($serviceName, new Reference($serviceName)));
        }
    }
}
