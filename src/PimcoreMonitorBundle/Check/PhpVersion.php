<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use InvalidArgumentException;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Traversable;

class PhpVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'system:php_version';

    protected array $version;

    protected string $operator = '>=';

    /**
     *
     * @param Traversable|array|string $expectedVersion The expected version
     * @param string                   $operator        One of: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Traversable|array|string $expectedVersion, string $operator = '>=')
    {
        if (is_object($expectedVersion)) {
            if (! $expectedVersion instanceof \Traversable) {
                throw new InvalidArgumentException(
                    'Expected version number as string, array or traversable, got ' . get_class($expectedVersion)
                );
            }

            $this->version = iterator_to_array($expectedVersion);
        } elseif (! is_scalar($expectedVersion)) {
            if (! is_array($expectedVersion)) {
                throw new InvalidArgumentException(
                    'Expected version number as string, array or traversable, got ' . gettype($expectedVersion)
                );
            }

            $this->version = $expectedVersion;
        } else {
            $this->version = [$expectedVersion];
        }

        if (! is_scalar($operator)) {
            throw new InvalidArgumentException(
                'Expected comparison operator as a string, got ' . gettype($operator)
            );
        }

        if (! in_array($operator, ['<', 'lt', '<=', 'le', '>', 'gt', '>=', 'ge', '==', '=', 'eq', '!=', '<>', 'ne'])) {
            throw new InvalidArgumentException(
                'Unknown comparison operator ' . $operator
            );
        }

        $this->operator = $operator;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        foreach ($this->version as $version) {
            if (! version_compare(PHP_VERSION, $version, $this->operator)) {
                return new Failure(sprintf(
                    'Current PHP version is %s, expected %s %s',
                    PHP_VERSION,
                    $this->operator,
                    $version
                ), PHP_VERSION);
            }
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
