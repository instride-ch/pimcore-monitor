<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('pimcore_monitor');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode->append($this->buildReportNode());
        $rootNode->append($this->buildChecksNode());

        return $treeBuilder;
    }

    private function buildReportNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('report');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode('api_key')
                    ->info('API key for health report endpoint.')
                    ->isRequired()
                ->end()
                ->scalarNode('default_endpoint')
                    ->info('Default health report API endpoint to send data to.')
                    ->isRequired()
                ->end()
            ->end();

        return $rootNode;
    }

    private function buildChecksNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder('checks');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('app_environment')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('environment')
                            ->info('The environment the application is running in.')
                            ->defaultValue('%kernel.environment%')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('disk_usage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('warning_threshold')
                            ->info('The warning threshold for disk usage in percent.')
                            ->defaultValue(90)
                            ->isRequired()
                        ->end()
                        ->integerNode('critical_threshold')
                            ->info('The critical threshold for disk usage in percent.')
                            ->defaultValue(95)
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('hosting_size')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('warning_threshold')
                            ->info('The warning threshold for the hosting size in bytes.')
                            ->defaultValue(48318382080)
                            ->isRequired()
                        ->end()
                        ->integerNode('critical_threshold')
                            ->info('The critical threshold for the hosting size in bytes.')
                            ->defaultValue(53687091200)
                            ->isRequired()
                        ->end()
                        ->scalarNode('path')
                            ->info('The root directory of the hosting.')
                            ->defaultValue('%kernel.project_dir%')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('mysql_version')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('version')
                            ->info('The expected version.')
                            ->defaultValue('10.5')
                            ->isRequired()
                        ->end()
                        ->scalarNode('operator')
                            ->info('One of: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne')
                            ->defaultValue('>=')
                            ->isRequired()
                            ->validate()
                                ->ifNotInArray(['<', 'lt', '<=', 'le', '>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=', '<>', 'ne'])
                                ->thenInvalid('Unknown comparison operator %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('php_version')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('version')
                            ->info('The expected version.')
                            ->defaultValue('8.0')
                            ->isRequired()
                        ->end()
                        ->scalarNode('operator')
                            ->info('One of: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne')
                            ->defaultValue('>=')
                            ->isRequired()
                            ->validate()
                                ->ifNotInArray(['<', 'lt', '<=', 'le', '>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=', '<>', 'ne'])
                                ->thenInvalid('Unknown comparison operator %s')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore_element_count')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('warning_threshold')
                            ->info('The warning threshold for amount of Pimcore elements.')
                            ->defaultValue(100000)
                            ->isRequired()
                        ->end()
                        ->integerNode('critical_threshold')
                            ->info('The critical threshold for amount of Pimcore elements.')
                            ->defaultValue(150000)
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
}
