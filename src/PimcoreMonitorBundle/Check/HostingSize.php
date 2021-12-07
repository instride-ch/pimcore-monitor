<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class HostingSize extends AbstractCheck
{
    protected const IDENTIFIER = 'device:hosting_size';

    private int $warningThreshold;
    private int $criticalThreshold;
    private string $path;

    public function __construct(int $warningThreshold, int $criticalThreshold, string $path)
    {
        $this->warningThreshold = $warningThreshold;
        $this->criticalThreshold = $criticalThreshold;
        $this->path = $path;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        $size = $this->getDirectorySize();

        if ($size >= $this->criticalThreshold) {
            return new Failure(sprintf('Hosting size is too high: %s', formatBytes($size)), [
                'path' => $this->path,
                'size' => formatBytes($size),
            ]);
        }

        if ($size >= $this->warningThreshold) {
            return new Warning(sprintf('Hosting size is high: %s', formatBytes($size)), [
                'path' => $this->path,
                'size' => formatBytes($size),
            ]);
        }

        return new Success(sprintf('Hosting size is %s', formatBytes($size)), [
            'path' => $this->path,
            'size' => formatBytes($size),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'Hosting Size';
    }

    /**
     * Returns the size of the specified directory path
     */
    private function getDirectorySize(): int
    {
        $io = popen('/usr/bin/du -sk ' . $this->path, 'r');
        $size = fgets($io, 4096);
        $size = (int) substr($size, 0, strpos($size, "\t"));
        pclose($io);

        return $size * 1024;
    }
}
