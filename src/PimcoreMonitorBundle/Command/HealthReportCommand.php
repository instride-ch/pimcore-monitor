<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Command;

use Laminas\Diagnostics\Runner\Reporter\BasicConsole;
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
use Symfony\Contracts\HttpClient\ResponseInterface;
use Wvision\Bundle\PimcoreMonitorBundle\Manager\RunnerManager;
use Wvision\Bundle\PimcoreMonitorBundle\Reporter\ArrayReporter;

class HealthReportCommand extends Command
{
    protected static $defaultName = 'pimcore:monitor:health-report';

    private string $reportEndpoint;
    private string $apiKey;
    private array $pimcoreSystemConfig;
    private string $secret;
    private HttpClientInterface $httpClient;
    private RunnerManager $runnerManager;

    public function __construct(
        string $reportEndpoint,
        string $apiKey,
        array $pimcoreSystemConfig,
        string $secret,
        HttpClientInterface $httpClient,
        RunnerManager $runnerManager
    ) {
        $this->reportEndpoint = $reportEndpoint;

        parent::__construct();

        $this->apiKey = $apiKey;
        $this->pimcoreSystemConfig = $pimcoreSystemConfig;
        $this->secret = $secret;
        $this->httpClient = $httpClient;
        $this->runnerManager = $runnerManager;
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Run all checks and send them to your statistics receiving endpoint.')
            ->addOption(
                'endpoint',
                null,
                InputOption::VALUE_REQUIRED,
                'Overwrite the default report endpoint',
                $this->reportEndpoint
            )
            ->setHelp('This command runs all checks and sends them to the defined report endpoint.');
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $instanceId = $this->getInstanceId();
        $hostDomain = $this->pimcoreSystemConfig['general']['domain'];

        if (null === $instanceId) {
            $output->writeln('<comment>Please define the secret parameter.</comment>');

            return Command::FAILURE;
        }

        if (empty($hostDomain)) {
            $output->writeln('<comment>Please define the main domain.</comment>');

            return Command::FAILURE;
        }

        $checkReporter = new ArrayReporter();

        $runner = $this->runnerManager->getRunner();
        $runner->addReporter($checkReporter);
        $runner->addReporter(new BasicConsole());
        $runner->run();

        try {
            $response = $this->httpClient->request('PUT', $input->getOption('endpoint'), [
                'auth_bearer' => $this->apiKey,
                'json' => [
                    'instance_id' => $instanceId,
                    'host_domain' => $hostDomain,
                    'checks' => $checkReporter->getResults(),
                ],
            ]);
            $payload = $response->toArray();
        } catch (TransportExceptionInterface | ClientExceptionInterface | DecodingExceptionInterface | RedirectionExceptionInterface | ServerExceptionInterface) {
            $response = null;
            $payload = null;
        }

        if (!$response instanceof ResponseInterface) {
            $output->writeln('<error>Sending the data to the endpoint failed!</error>');

            return Command::FAILURE;
        }

        $output->writeln(sprintf('<question>%s: %s</question>', $payload['status'], $payload['message']));

        return $response->getStatusCode() === 200 ? Command::SUCCESS : Command::FAILURE;
    }

    private function getInstanceId(): ?string
    {
        if (empty($this->secret)) {
            return null;
        }

        try {
            $instanceId = sha1(substr($this->secret, 3, -3));
        } catch (\Exception) {
            $instanceId = null;
        }

        return $instanceId;
    }
}
