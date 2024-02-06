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

namespace Instride\Bundle\PimcoreMonitorBundle;

use Pimcore\Extension\Bundle\AbstractPimcoreBundle;
use Pimcore\Extension\Bundle\Traits\PackageVersionTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Instride\Bundle\PimcoreMonitorBundle\Check\CheckInterface;
use Instride\Bundle\PimcoreMonitorBundle\DependencyInjection\Compiler\CheckTagCompilerPass;

class PimcoreMonitorBundle extends AbstractPimcoreBundle
{
    use PackageVersionTrait;

    /**
     * {@inheritDoc}
     */
    public function getNiceName(): string
    {
        return 'Pimcore Monitor';
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

    /**
     * {@inheritDoc}
     */
    protected function getComposerPackageName(): string
    {
        return 'instride/pimcore-monitor';
    }
}
