<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Carbon\Carbon;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Maintenance\ExecutorInterface;

class PimcoreMaintenance extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:maintenance';

    private ExecutorInterface $maintenanceExecutor;

    public function __construct(ExecutorInterface $maintenanceExecutor)
    {
        $this->maintenanceExecutor = $maintenanceExecutor;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        $lastExecution = $this->maintenanceExecutor->getLastExecution();
        $lastRun = Carbon::createFromTimestampUTC($lastExecution);
        $data = [
            'active' => false,
            'last_execution' => $lastRun instanceof Carbon ? $lastRun->toIso8601String() : $lastExecution,
        ];

        // Maintenance script should run at least every hour + a little tolerance
        if ($lastExecution && (time() - $lastExecution) < 3660) {
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
