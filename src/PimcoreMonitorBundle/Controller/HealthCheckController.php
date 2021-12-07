<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Controller;

use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Wvision\Bundle\PimcoreMonitorBundle\Manager\RunnerManager;
use Wvision\Bundle\PimcoreMonitorBundle\Reporter\ArrayReporter;

class HealthCheckController extends FrontendController
{
    public function health(RunnerManager $runnerManager): JsonResponse
    {
        $reporter = new ArrayReporter();

        $runner = $runnerManager->getRunner();
        $runner->addReporter($reporter);
        $runner->run();

        $results = $reporter->getResults();

        return $this->json($results);
    }
}
