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
 * @copyright  2024 instride AG (https://instride.ch)
 * @license    https://github.com/instride-ch/pimcore-monitor/blob/main/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Instride\Bundle\PimcoreMonitorBundle\Check;

use Doctrine\DBAL\Connection;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;

class MySqlVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'system:mysql_version';

    public function __construct(
        protected bool $skip,
        protected string $expectedVersion,
        protected string $operator,
        protected Connection $connection
    ) {}

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        try {
            $mysqlVersion = $this->connection->fetchOne('SELECT VERSION()');
        } catch (\Exception) {
            $mysqlVersion = null;
        }

        if (! \version_compare($mysqlVersion, $this->expectedVersion, $this->operator)) {
            return new Failure(\sprintf(
                'Current MySQL version is %s, expected %s %s',
                $mysqlVersion,
                $this->operator,
                $this->expectedVersion
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
