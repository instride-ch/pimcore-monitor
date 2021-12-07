<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CheckTagCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('pimcore_monitor.check') as $id => $tags) {
            foreach ($tags as $attributes) {
                $alias = empty($attributes['alias']) ? $id : $attributes['alias'];

                $runnerDefinition = $container->getDefinition('pimcore_monitor.runner');
                $runnerDefinition->addMethodCall('addCheck', [new Reference($id), $alias]);
            }
        }
    }
}
