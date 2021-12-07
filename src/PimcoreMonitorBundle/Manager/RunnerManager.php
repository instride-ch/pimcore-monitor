<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Manager;

use Laminas\Diagnostics\Runner\Runner;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RunnerManager
{
    protected ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getRunner(): Runner
    {
        return $this->container->get('pimcore_monitor.runner');
    }
}
