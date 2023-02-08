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

namespace Wvision\Bundle\PimcoreMonitorBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Wvision\Bundle\PimcoreMonitorBundle\Check\CheckInterface;
use Wvision\Bundle\PimcoreMonitorBundle\DependencyInjection\Compiler\CheckTagCompilerPass;

class PimcoreMonitorBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * {@inheritDoc}
     */
    protected function getComposerPackageName(): string
    {
        return 'w-vision/pimcore-monitor-bundle';
    }

    /**
     * {@inheritDoc}
     */
    public function getNiceName(): string
    {
        return 'Pimcore Monitor Bundle';
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Monitor your Pimcore installation';
    }

    /**
     * {@inheritDoc}
     */
    public function getCssPaths(): array
    {
        return [
            '/bundles/pimcoremonitor/pimcore/css/icons.css',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getJsPaths(): array
    {
        return [
            '/bundles/pimcoremonitor/pimcore/js/startup.js',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function build(ContainerBuilder $container): void
    {
        if (\method_exists($container, 'registerForAutoconfiguration')) {
            $container
                ->registerForAutoconfiguration(CheckInterface::class)
                ->addTag('pimcore_monitor.check');
        }

        $container->addCompilerPass(new CheckTagCompilerPass());
    }
}
