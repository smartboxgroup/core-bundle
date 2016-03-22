<?php

namespace Smartbox\CoreBundle\DependencyInjection;

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

        $serviceIds = $container->findTaggedServiceIds('smartcore.smoke_test');
        foreach ($serviceIds as $serviceId => $tags) {
            foreach($tags as $tag => $attr){
                $runMethod = 'run';
                $descriptionMethod = 'getDescription';

                if(array_key_exists('method',$attr)){
                    $runMethod = $attr['runMethod'];
                }

                if(array_key_exists('descriptionMethod',$attr)){
                    $descriptionMethod = $attr['descriptionMethod'];
                }

                $smokeTestCommand->addMethodCall('addTest', [$serviceId, new Reference($serviceId),$runMethod, $descriptionMethod]);
            }
        }
    }
}
