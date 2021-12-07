<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use InvalidArgumentException;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Db\ConnectionInterface;
use Traversable;

class MySqlVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'system:mysql_version';

    protected array $version;
    protected string $operator = '>=';
    protected ConnectionInterface $db;

    /**
     *
     * @param Traversable|array|string $expectedVersion The expected version
     * @param string                   $operator        One of: <, lt, <=, le, >, gt, >=, ge, ==, =, eq, !=, <>, ne
     *
     * @throws InvalidArgumentException
     */
    public function __construct(Traversable|array|string $expectedVersion, ConnectionInterface $db, string $operator = '>=')
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
        $this->db = $db;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        try {
            $mysqlVersion = $this->db->fetchOne('SELECT VERSION()');
        } catch (\Exception) {
            $mysqlVersion = null;
        }

        foreach ($this->version as $version) {
            if (! version_compare($mysqlVersion, $version, $this->operator)) {
                return new Failure(sprintf(
                    'Current MySQL version is %s, expected %s %s',
                    $mysqlVersion,
                    $this->operator,
                    $version
                ), $mysqlVersion);
            }
        }

        return new Success('Current MySQL version is ' . $mysqlVersion, $mysqlVersion);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'MySQL Version';
    }
}
