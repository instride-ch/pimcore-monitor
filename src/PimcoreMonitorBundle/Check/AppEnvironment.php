<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

class AppEnvironment extends AbstractCheck
{
    protected const IDENTIFIER = 'system:app_environment';

    protected string $environment;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ('prod' !== $this->environment) {
            return new Failure('Application is not running in production mode');
        }

        return new Success('Application is running in production mode');
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'App Environment';
    }
}
