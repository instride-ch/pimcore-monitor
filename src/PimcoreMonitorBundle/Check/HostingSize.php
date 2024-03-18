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
use Laminas\Diagnostics\Result\Warning;

class HostingSize extends AbstractCheck
{
    protected const IDENTIFIER = 'device:hosting_size';

    public function __construct(
        protected bool $skip,
        protected int $warningThreshold,
        protected int $criticalThreshold,
        protected string $path
    ) {}

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $size = $this->getDirectorySize();
        $data = [
            'path' => $this->path,
            'size' => \formatBytes($size),
        ];

        if ($size >= $this->criticalThreshold) {
            return new Failure(\sprintf('Hosting size is too high: %s', \formatBytes($size)), $data);
        }

        if ($size >= $this->warningThreshold) {
            return new Warning(\sprintf('Hosting size is high: %s', \formatBytes($size)), $data);
        }

        return new Success(sprintf('Hosting size is %s', \formatBytes($size)), $data);
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
        $io = \popen('/usr/bin/du -sk ' . $this->path, 'r');
        $size = \fgets($io, 4096);
        $size = (int) \substr($size, 0, \strpos($size, "\t"));
        \pclose($io);

        return $size * 1024;
    }
}
