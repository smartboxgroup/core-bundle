<?php

namespace Smartbox\CoreBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SmartboxCoreExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if(empty($config['fixtures_path'])){
            $config['fixtures_path'] = $container->getParameter('kernel.root_dir').'/Resources/Fixtures';
        }

        if(empty($config['entities_namespaces'])){
            $config['entities_namespaces'] = array();
        }
        $config['entities_namespaces'][] = 'Smartbox\CoreBundle\Type';

        $container->setParameter('smartcore.fixtures_path',$config['fixtures_path']);
        $container->setParameter('smartcore.entity.namespaces',$config['entities_namespaces']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }
}
