<?php

namespace KMGi\CommonBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class KMGiCommonExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'kmgi_common';
    }

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('kmgi_common.layout_template.subfolder', $config['layout_template']['subfolder']);
        $container->setParameter('kmgi_common.layout_template.filename', $config['layout_template']['filename']);
        $container->setParameter('kmgi_common.controller.subfolder', $config['controller']['subfolder']);
        $container->setParameter('kmgi_common.controller.route_name_prefix', $config['controller']['route_name_prefix']);
    }
}
