<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Logger;
use Symfony\Component\HttpFoundation\Response;
use Wvision\Bundle\PimcoreMonitorBundle\Manager\RunnerManager;
use Wvision\Bundle\PimcoreMonitorBundle\Reporter\ArrayReporter;

class HealthCheckController extends AdminController
{
    public function status(RunnerManager $runnerManager): Response
    {
        $adminUser = $this->getAdminUser();

        // Check rights
        if (!$adminUser || !$adminUser->isAdmin()) {
            Logger::error(
                'User {user} attempted to access the system health status page, but has no permission to do so',
                ['user' => $adminUser->getName()]
            );

            throw $this->createAccessDeniedHttpException();
        }

        $reporter = new ArrayReporter(true);

        $runner = $runnerManager->getRunner();
        $runner->addReporter($reporter);
        $runner->run();

        $results = $reporter->getResults();

        return $this->render('@PimcoreMonitor/Admin/Health/status.html.twig', [
            'results' => $results,
        ]);
    }
}
