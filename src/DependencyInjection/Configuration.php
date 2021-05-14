<?php

namespace KMGi\CommonBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('kmgi_common');

        $rootNode
            ->children()
                ->arrayNode('controller')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('route_name_prefix')
                            ->defaultValue('admin')
                        ->end()
                        ->scalarNode('subfolder')
                            ->defaultValue('Admin')
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('layout_template')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('subfolder')
                            ->defaultValue('Admin')
                        ->end()
                        ->scalarNode('filename')
                            ->defaultValue('layout.html.twig')
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
