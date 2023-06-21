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

namespace Wvision\Bundle\PimcoreMonitorBundle\Controller\Admin;

use Pimcore\Bundle\AdminBundle\Controller\AdminController;
use Pimcore\Logger;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Wvision\Bundle\PimcoreMonitorBundle\Manager\RunnerManager;
use Wvision\Bundle\PimcoreMonitorBundle\Reporter\ArrayReporter;

class HealthCheckController extends AdminController
{
    public function health(RunnerManager $runnerManager): JsonResponse
    {
        $adminUser = $this->getAdminUser();

        // Check rights
        if (!$adminUser || !$adminUser->isAdmin()) {
            Logger::error(
                'User {user} attempted to access the system health report results, but has no permission to do so.',
                ['user' => $adminUser->getName()]
            );

            throw $this->createAccessDeniedHttpException();
        }

        $reporter = new ArrayReporter();

        $runner = $runnerManager->getRunner();
        $runner->addReporter($reporter);
        $runner->run();

        $results = $reporter->getResults();

        return $this->json($results);
    }

    public function status(RunnerManager $runnerManager): Response
    {
        $adminUser = $this->getAdminUser();

        // Check rights
        if (!$adminUser || !$adminUser->isAdmin()) {
            Logger::error(
                'User {user} attempted to access the system health status page, but has no permission to do so.',
                ['user' => $adminUser->getName()]
            );

            throw $this->createAccessDeniedHttpException();
        }

        $reporter = new ArrayReporter(true);

        $runner = $runnerManager->getRunner();
        $runner->addReporter($reporter);
        $runner->run();

        $results = $reporter->getResults();

        return $this->render('@PimcoreMonitor/admin/health/status.html.twig', [
            'results' => $results,
        ]);
    }
}
