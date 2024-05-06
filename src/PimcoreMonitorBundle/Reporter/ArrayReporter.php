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

namespace Instride\Bundle\PimcoreMonitorBundle\Reporter;

use Instride\Bundle\PimcoreMonitorBundle\Check\CheckInterface;
use Laminas\Diagnostics\Check\CheckInterface as BaseCheckInterface;
use Laminas\Diagnostics\Result\Collection as ResultsCollection;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\SkipInterface;
use Laminas\Diagnostics\Result\SuccessInterface;
use Laminas\Diagnostics\Result\WarningInterface;
use Laminas\Diagnostics\Runner\Reporter\ReporterInterface;

class ArrayReporter implements ReporterInterface
{
    public const STATUS_OK = 'OK';
    public const STATUS_KO = 'KO';

    protected string $globalStatus = self::STATUS_OK;
    protected array $results = [];

    public function __construct(
        protected bool $flattenOutput = false,
        protected array $excludeChecks = [],
        protected array $includeChecks = []
    ) {}

    public function getResults(): array
    {
        return $this->results;
    }

    public function getGlobalStatus(): string
    {
        return $this->globalStatus;
    }

    public function onStart(\ArrayObject $checks, $runnerConfig): void
    {
        if (empty($this->excludeChecks)) {
            return;
        }

        foreach ($this->excludeChecks as $checkAlias) {
            $checks->offsetUnset($checkAlias);
        }
    }

    public function onBeforeRun(BaseCheckInterface|CheckInterface $check, $checkAlias = null): bool
    {
        if (empty($this->includeChecks)) {
            return true;
        }

        return \in_array($checkAlias, $this->includeChecks, true);
    }

    public function onAfterRun(
        BaseCheckInterface|CheckInterface $check,
        ResultInterface $result,
        $checkAlias = null
    ): void {
        if (!$check instanceof CheckInterface) {
            return;
        }

        switch (true) {
            case $result instanceof SuccessInterface:
                $status = 0;
                $statusName = 'check_result_ok';
                break;

            case $result instanceof WarningInterface:
                $status = 1;
                $statusName = 'check_result_warning';
                $this->globalStatus = self::STATUS_KO;
                break;

            case $result instanceof SkipInterface:
                $status = 2;
                $statusName = 'check_result_skip';
                break;

            default:
                $status = 3;
                $statusName = 'check_result_critical';
                $this->globalStatus = self::STATUS_KO;
        }

        $data = [
            'check_id' => $checkAlias,
            'check_name' => $check->getLabel(),
            'status_code' => $status,
            'status_name' => $statusName,
            'message' => $result->getMessage(),
            'data' => $result->getData(),
        ];

        if (true === $this->flattenOutput) {
            $this->results[] = $data;
        } else {
            $temp =& $this->results;
            foreach (\explode(':', $check->getIdentifier()) as $key) {
                $temp =& $temp[$key];
            }

            $temp = $data;
        }
    }

    public function onStop(ResultsCollection $results): void
    {
    }

    public function onFinish(ResultsCollection $results): void
    {
        if (false === $this->flattenOutput) {
            $this->results['_summary'] = [
                'status' => $this->getGlobalStatus(),
                'success' => $results->getSuccessCount(),
                'warning' => $results->getWarningCount(),
                'failure' => $results->getFailureCount(),
                'skip' => $results->getSkipCount(),
                'unknown' => $results->getUnknownCount(),
            ];
        }
    }
}
