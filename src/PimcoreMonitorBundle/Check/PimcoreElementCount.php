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
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class PimcoreElementCount extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:element_count';

    public function __construct(
        protected bool $skip,
        protected int $warningThreshold,
        protected int $criticalThreshold,
        protected Connection $connection
    ) {}

    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $documentCount = $this->getTableRowCount('documents', 'id');
        $assetCount = $this->getTableRowCount('assets', 'id');
        $objectCount = $this->getTableRowCount('objects', 'id');
        $totalCount = $documentCount + $assetCount + $objectCount;

        $data = [
            'documents' => $documentCount,
            'assets' => $assetCount,
            'objects' => $objectCount,
            'total' => $totalCount,
        ];

        if ($totalCount >= $this->criticalThreshold) {
            return new Failure(\sprintf('Element count too high: %s', $totalCount), $data);
        }

        if ($totalCount >= $this->warningThreshold) {
            return new Warning(\sprintf('Element count high: %s', $totalCount), $data);
        }

        return new Success(\sprintf('There are %s Pimcore elements in the system', $totalCount), $data);
    }

    public function getLabel(): string
    {
        return 'Pimcore Element Count';
    }

    protected function getTableRowCount(string $tableName, string $idColumn): int
    {
        $qb = $this->connection->createQueryBuilder()
            ->select("COUNT(t.$idColumn)")
            ->from($tableName, 't');

        try {
            $count = $qb->executeQuery()->fetchOne();
        } catch (DBALException) {
            $count = 0;
        }

        return (int) $count;
    }
}
