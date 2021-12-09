<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class PimcoreMonitorExtension extends Extension
{
    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // Register report parameters
        foreach ($config['report'] as $confName => $confValue) {
            $container->setParameter(
                sprintf('pimcore_monitor.report.%s', $confName),
                $confValue
            );
        }

        // Register checks parameters
        foreach ($config['checks'] as $checkName => $checkConfig) {
            foreach ($checkConfig as $confName => $confValue) {
                $container->setParameter(
                    sprintf('pimcore_monitor.checks.%s.%s', $checkName, $confName),
                    $confValue
                );
            }
        }

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }
}
