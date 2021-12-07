<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('pimcore_monitor');
        $treeBuilder->getRootNode()
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('api_key')
                    ->info('API key for health report endpoint.')
                    ->isRequired()
                ->end()
                ->scalarNode('default_report_endpoint')
                    ->info('Default health report API endpoint to send data to.')
                    ->isRequired()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
