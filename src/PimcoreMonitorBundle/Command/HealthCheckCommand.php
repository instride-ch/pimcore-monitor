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

namespace Wvision\Bundle\PimcoreMonitorBundle\Command;

use Laminas\Diagnostics\Runner\Reporter\BasicConsole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wvision\Bundle\PimcoreMonitorBundle\Manager\RunnerManager;

class HealthCheckCommand extends Command
{
    protected static $defaultName = 'pimcore:monitor:health-check';

    public function __construct(private RunnerManager $runnerManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $runner = $this->runnerManager->getRunner();
        $runner->addReporter(new BasicConsole());
        $results = $runner->run();

        if (($results->getFailureCount() + $results->getWarningCount()) > 0) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
