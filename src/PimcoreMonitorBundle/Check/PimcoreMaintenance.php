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

use Carbon\Carbon;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Maintenance\ExecutorInterface;

class PimcoreMaintenance extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:maintenance';

    public function __construct(protected bool $skip, protected ExecutorInterface $maintenanceExecutor) {}

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $lastExecution = $this->maintenanceExecutor->getLastExecution();
        $lastRun = Carbon::createFromTimestampUTC($lastExecution);
        $data = [
            'active' => false,
            'last_execution' => $lastRun instanceof Carbon ? $lastRun->toIso8601String() : $lastExecution,
        ];

        // Maintenance script should run at least every hour + a little tolerance
        if ($lastExecution && (\time() - $lastExecution) < 3660) {
            $data['active'] = true;

            return new Success('Pimcore maintenance is activated', $data);
        }

        return new Failure('Pimcore maintenance is not activated', $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'Pimcore Maintenance';
    }
}
