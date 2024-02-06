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

interface CheckInterface
{
    /**
     * Return a unique ID describing this test instance.
     *
     * @return string
     */
    public function getIdentifier(): string;
}
