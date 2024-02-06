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
 * @copyright  2024 instride AG (https://instride.ch)
 * @license    https://github.com/instride-ch/PimcoreMonitorBundle/blob/main/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Instride\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Extension\Bundle\PimcoreBundleManager;

class PimcoreBundles extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:bundles';

    public function __construct(protected bool $skip, protected PimcoreBundleManager $pimcoreBundleManager) {}

    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

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

        return new Success(\sprintf('There are %s Pimcore bundles in the system', \count($bundles)), $bundles);
    }

    public function getLabel(): string
    {
        return 'Pimcore Bundles';
    }
}
