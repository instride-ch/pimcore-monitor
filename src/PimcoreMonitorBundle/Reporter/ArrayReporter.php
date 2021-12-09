<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Reporter;

use Laminas\Diagnostics\Check\CheckInterface as BaseCheckInterface;
use Laminas\Diagnostics\Result\Collection as ResultsCollection;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\SkipInterface;
use Laminas\Diagnostics\Result\SuccessInterface;
use Laminas\Diagnostics\Result\WarningInterface;
use Laminas\Diagnostics\Runner\Reporter\ReporterInterface;
use Wvision\Bundle\PimcoreMonitorBundle\Check\CheckInterface;

class ArrayReporter implements ReporterInterface
{
    public const STATUS_OK = 'OK';
    public const STATUS_KO = 'KO';

    protected string $globalStatus = self::STATUS_OK;
    protected array $results = [];
    protected bool $flattenOutput;
    protected array $excludeChecks;
    protected array $includeChecks;

    public function __construct(bool $flattenOutput = false, array $excludeChecks = [], array $includeChecks = [])
    {
        $this->flattenOutput = $flattenOutput;
        $this->excludeChecks = $excludeChecks;
        $this->includeChecks = $includeChecks;
    }

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

    public function onAfterRun(BaseCheckInterface|CheckInterface $check, ResultInterface $result, $checkAlias = null)
    {
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
            foreach (explode(':', $check->getIdentifier()) as $key) {
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
