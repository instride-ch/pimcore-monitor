<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Db\ConnectionInterface;

class MySqlVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'system:mysql_version';

    protected bool $skip;
    protected string $version;
    protected string $operator;
    protected ConnectionInterface $db;

    public function __construct(bool $skip, string $expectedVersion, string $operator, ConnectionInterface $db)
    {
        $this->skip = $skip;
        $this->version = $expectedVersion;
        $this->operator = $operator;
        $this->db = $db;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        try {
            $mysqlVersion = $this->db->fetchOne('SELECT VERSION()');
        } catch (\Exception) {
            $mysqlVersion = null;
        }

        if (! version_compare($mysqlVersion, $this->version, $this->operator)) {
            return new Failure(sprintf(
                'Current MySQL version is %s, expected %s %s',
                $mysqlVersion,
                $this->operator,
                $this->version
            ), $mysqlVersion);
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
