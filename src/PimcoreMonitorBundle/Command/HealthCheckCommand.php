<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Command;

use Laminas\Diagnostics\Runner\Reporter\BasicConsole;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wvision\Bundle\PimcoreMonitorBundle\Manager\RunnerManager;

class HealthCheckCommand extends Command
{
    protected static $defaultName = 'pimcore:monitor:health-check';

    private RunnerManager $runnerManager;

    public function __construct(RunnerManager $runnerManager)
    {
        parent::__construct();

        $this->runnerManager = $runnerManager;
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
