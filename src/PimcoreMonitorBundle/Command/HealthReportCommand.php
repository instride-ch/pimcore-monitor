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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Wvision\Bundle\PimcoreMonitorBundle\Manager\RunnerManager;
use Wvision\Bundle\PimcoreMonitorBundle\Reporter\ArrayReporter;

class HealthReportCommand extends Command
{
    protected static $defaultName = 'pimcore:monitor:health-report';

    public function __construct(
        private string $reportEndpoint,
        private string $apiKey,
        private string $instanceEnvironment,
        private array $systemConfig,
        private string $secret,
        private HttpClientInterface $httpClient,
        private RunnerManager $runnerManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Run all checks and send them to your statistics receiving endpoint.')
            ->addOption(
                'endpoint',
                null,
                InputOption::VALUE_REQUIRED,
                'Overwrite the default endpoint to send the report data to.',
                $this->reportEndpoint
            )
            ->addOption(
                'exclude',
                'ex',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'List any task alias that you want to exclude from execution.'
            )
            ->addOption(
                'include',
                'in',
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'List any task alias that you want to execute.'
            )
            ->setHelp('This command runs all checks and sends them to the defined report endpoint.');
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $instanceId = $this->getInstanceId();
        $hostDomain = $this->systemConfig['general']['domain'];

        if (null === $instanceId) {
            $output->writeln('<comment>Please define the secret parameter.</comment>');

            return Command::FAILURE;
        }

        if (empty($hostDomain)) {
            $output->writeln('<comment>Please define the main domain.</comment>');

            return Command::FAILURE;
        }

        if (empty($this->instanceEnvironment)) {
            $this->instanceEnvironment = 'prod';
        }

        $checkReporter = new ArrayReporter(
            false,
            $input->getOption('exclude'),
            $input->getOption('include')
        );

        $runner = $this->runnerManager->getRunner();
        $runner->addReporter($checkReporter);
        $runner->run();

        try {
            $response = $this->httpClient->request('PUT', $input->getOption('endpoint'), [
                'auth_bearer' => $this->apiKey,
                'json' => [
                    'instance_id'=> $instanceId,
                    'checks' => $checkReporter->getResults(),
                    'metadata' => [
                        'host_domain' => $hostDomain,
                        'instance_environment' => $this->instanceEnvironment,
                    ]
                ],
            ]);
            $payload = $response->toArray();
        } catch (
            TransportExceptionInterface |
            ClientExceptionInterface |
            DecodingExceptionInterface |
            RedirectionExceptionInterface |
            ServerExceptionInterface $e
        ) {
            $jsonResponse = \json_decode($response->getContent(false), true);

            $output->writeln(
                \sprintf(
                    '<error>Sending the data to the endpoint failed!</error> – %s – %s',
                    ($jsonResponse) ? $jsonResponse['message'] : 'no json response',
                    $e->getMessage()
                )
            );

            return Command::FAILURE;
        }

        $output->writeln(\sprintf('<question>%s: %s</question>', $payload['status'], $payload['message']));

        return $response->getStatusCode() === 200 ? Command::SUCCESS : Command::FAILURE;
    }

    private function getInstanceId(): ?string
    {
        if (empty($this->secret)) {
            return null;
        }

        try {
            $instanceId = \sha1(\substr($this->secret, 3, -3));
        } catch (\Exception) {
            $instanceId = null;
        }

        return $instanceId;
    }
}
