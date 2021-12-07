<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use InvalidArgumentException;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class DiskUsage extends AbstractCheck
{
    protected const IDENTIFIER = 'device:disk_usage';

    private int $warningThreshold;
    private int $criticalThreshold;
    private string $path;

    public function __construct(int $warningThreshold, int $criticalThreshold, string $path = '/')
    {
        if ($warningThreshold > 100 || $warningThreshold < 0) {
            throw new InvalidArgumentException(
                'Invalid warningThreshold argument - expecting an integer between 1 and 100'
            );
        }

        if ($criticalThreshold > 100 || $criticalThreshold < 0) {
            throw new InvalidArgumentException(
                'Invalid criticalThreshold argument - expecting an integer between 1 and 100'
            );
        }

        $this->warningThreshold = $warningThreshold;
        $this->criticalThreshold = $criticalThreshold;
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
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
