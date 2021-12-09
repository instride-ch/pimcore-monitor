<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use InvalidArgumentException;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;

class PhpVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'system:php_version';

    protected string $version;
    protected string $operator;

    /**
     *
     * @param string $expectedVersion The expected version
     * @param string $operator        One of: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $expectedVersion, string $operator)
    {
        $this->version = $expectedVersion;
        $this->operator = $operator;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if (! version_compare(PHP_VERSION, $this->version, $this->operator)) {
            return new Failure(sprintf(
                'Current PHP version is %s, expected %s %s',
                PHP_VERSION,
                $this->operator,
                $this->version
            ), PHP_VERSION);
        }

        return new Success('Current PHP version is ' . PHP_VERSION, PHP_VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'PHP Version';
    }
}
