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

namespace Instride\Bundle\PimcoreMonitorBundle\Manager;

use Laminas\Diagnostics\Runner\Runner;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RunnerManager
{
    public function __construct(protected ContainerInterface $container) {}

    public function getRunner(): Runner
    {
        return $this->container->get('pimcore_monitor.runner');
    }
}
