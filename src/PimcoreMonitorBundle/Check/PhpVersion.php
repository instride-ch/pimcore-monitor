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

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;

class PhpVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'system:php_version';

    protected bool $skip;
    protected string $version;
    protected string $operator;

    public function __construct(bool $skip, string $expectedVersion, string $operator)
    {
        $this->skip = $skip;
        $this->version = $expectedVersion;
        $this->operator = $operator;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        if (! version_compare(PHP_VERSION, $this->version, $this->operator)) {
            return new Failure(sprintf(
                'Current PHP version is %s, expected %s %s',
                PHP_VERSION,
                $this->operator,
                $this->version
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
