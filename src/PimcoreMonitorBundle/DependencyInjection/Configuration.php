<?php

declare(strict_types=1);

/**
 * Pimcore Monitor
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2022 w-vision AG (https://www.w-vision.ch)
 * @license    https://github.com/w-vision/PimcoreMonitorBundle/blob/master/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

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
                ->scalarNode('instance_environment')
                    ->info('Environment for project')
                    ->isRequired()
                    ->cannotBeEmpty()
                    ->defaultValue('prod')
                    ->example('prod, dev, test')
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
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
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
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                        ->integerNode('warning_threshold')
                            ->info('The warning threshold for disk usage in percent.')
                            ->defaultValue(90)
                            ->isRequired()
                            ->min(0)->max(100)
                        ->end()
                        ->integerNode('critical_threshold')
                            ->info('The critical threshold for disk usage in percent.')
                            ->defaultValue(95)
                            ->isRequired()
                            ->min(0)->max(100)
                        ->end()
                        ->scalarNode('path')
                            ->info('The root directory of the hosting.')
                            ->defaultValue('%kernel.project_dir%')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('doctrine_migrations')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('hosting_size')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                        ->integerNode('warning_threshold')
                            ->info('The warning threshold for the hosting size in bytes.')
                            ->defaultValue(48318382080) # 45 GB
                            ->isRequired()
                            ->min(0)
                        ->end()
                        ->integerNode('critical_threshold')
                            ->info('The critical threshold for the hosting size in bytes.')
                            ->defaultValue(53687091200) # 50 GB
                            ->isRequired()
                            ->min(0)
                        ->end()
                        ->scalarNode('path')
                            ->info('The root directory of the hosting.')
                            ->defaultValue('%kernel.project_dir%')
                            ->isRequired()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('database_size')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                        ->integerNode('warning_threshold')
                            ->info('The warning threshold for the database size in bytes.')
                            ->defaultValue(9878424780) # 9.2 GB
                            ->isRequired()
                            ->min(0)
                        ->end()
                        ->integerNode('critical_threshold')
                            ->info('The critical threshold for the database size in bytes.')
                            ->defaultValue(10737418240) # 10 GB
                            ->isRequired()
                            ->min(0)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('database_table_size')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                        ->integerNode('warning_threshold')
                            ->info('The warning threshold for all database tables size in bytes.')
                            ->defaultValue(943718400) # 900 MB
                            ->isRequired()
                            ->min(0)
                        ->end()
                        ->integerNode('critical_threshold')
                            ->info('The critical threshold for all database tables size in bytes.')
                            ->defaultValue(1073741824) # 1 GB
                            ->isRequired()
                            ->min(0)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('https_connection')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('mysql_version')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
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
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
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
                ->arrayNode('pimcore_areabricks')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore_bundles')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore_element_count')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                        ->integerNode('warning_threshold')
                            ->info('The warning threshold for amount of Pimcore elements.')
                            ->defaultValue(100000)
                            ->isRequired()
                            ->min(0)
                        ->end()
                        ->integerNode('critical_threshold')
                            ->info('The critical threshold for amount of Pimcore elements.')
                            ->defaultValue(150000)
                            ->isRequired()
                            ->min(0)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore_maintenance')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore_users')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('pimcore_version')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('enabled')
                            ->info('Enables this check globally.')
                            ->defaultValue(true)
                        ->end()
                        ->booleanNode('skip')
                            ->info('Skips this check globally.')
                            ->defaultValue(false)
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }
}
