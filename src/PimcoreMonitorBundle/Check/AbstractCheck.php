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

use Laminas\Diagnostics\Check\CheckInterface as BaseCheckInterface;

abstract class AbstractCheck implements BaseCheckInterface, CheckInterface
{
    protected const IDENTIFIER = '__undefined__';

    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return static::IDENTIFIER;
    }
}
