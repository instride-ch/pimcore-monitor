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
use Laminas\Diagnostics\Result\Warning;

class DiskUsage extends AbstractCheck
{
    protected const IDENTIFIER = 'device:disk_usage';

    protected bool $skip;
    protected int $warningThreshold;
    protected int $criticalThreshold;
    protected string $path;

    public function __construct(bool $skip, int $warningThreshold, int $criticalThreshold, string $path)
    {
        $this->skip = $skip;
        $this->warningThreshold = $warningThreshold;
        $this->criticalThreshold = $criticalThreshold;
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $df = disk_free_space($this->path);
        $dt = disk_total_space($this->path);
        $du = $dt - $df;
        $dp = ($du / $dt) * 100;

        if ($dp >= $this->criticalThreshold) {
            return new Failure(sprintf('Disk usage too high: %2d%%', $dp), [
                'used' => formatBytes($du),
                'free' => formatBytes($df),
                'total' => formatBytes($dt),
            ]);
        }

        if ($dp >= $this->warningThreshold) {
            return new Warning(sprintf('Disk usage high: %2d%%', $dp), [
                'used' => formatBytes($du),
                'free' => formatBytes($df),
                'total' => formatBytes($dt),
            ]);
        }

        return new Success(sprintf('Disk usage is %2d%%', $dp), [
            'used' => formatBytes($du),
            'free' => formatBytes($df),
            'total' => formatBytes($dt),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'Disk Usage';
    }
}
