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
 * @license    https://github.com/instride-ch/pimcore-monitor/blob/main/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Instride\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;

class PhpVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'system:php_version';

    public function __construct(protected bool $skip, protected string $expectedVersion, protected string $operator) {}

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        if (! \version_compare(PHP_VERSION, $this->expectedVersion, $this->operator)) {
            return new Failure(\sprintf(
                'Current PHP version is %s, expected %s %s',
                PHP_VERSION,
                $this->operator,
                $this->expectedVersion
            ), PHP_VERSION);
        }

        return new Success('Current PHP version is ' . PHP_VERSION, PHP_VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'PHP Version';
    }
}
