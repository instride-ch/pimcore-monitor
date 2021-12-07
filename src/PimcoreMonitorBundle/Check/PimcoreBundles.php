<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Extension\Bundle\PimcoreBundleManager;

class PimcoreBundles extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:bundles';

    protected PimcoreBundleManager $pimcoreBundleManager;

    public function __construct(PimcoreBundleManager $pimcoreBundleManager)
    {
        $this->pimcoreBundleManager = $pimcoreBundleManager;
    }

    public function check(): ResultInterface
    {
        $bundles = [];

        foreach ($this->pimcoreBundleManager->getActiveBundles() as $bundle) {
            $bundles[] = [
                'identifier' => $this->pimcoreBundleManager->getBundleIdentifier($bundle),
                'name' => $bundle->getNiceName(),
                'version' => $bundle->getVersion(),
                'is_enabled' => $this->pimcoreBundleManager->isEnabled($bundle),
                'is_installed' => $this->pimcoreBundleManager->isInstalled($bundle),
            ];
        }

        return new Success(sprintf('There are %s Pimcore bundles in the system', \count($bundles)), $bundles);
    }

    public function getLabel(): string
    {
        return 'Pimcore Bundles';
    }
}
