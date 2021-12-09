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

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use Pimcore\Db\ConnectionInterface;

class PimcoreElementCount extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:element_count';

    protected bool $skip;
    protected int $warningThreshold;
    protected int $criticalThreshold;
    protected ConnectionInterface $connection;

    public function __construct(bool $skip, int $warningThreshold, int $criticalThreshold, ConnectionInterface $connection)
    {
        $this->skip = $skip;
        $this->warningThreshold = $warningThreshold;
        $this->criticalThreshold = $criticalThreshold;
        $this->connection = $connection;
    }

    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $documentCount = $this->getTableRowCount('documents', 'id');
        $assetCount = $this->getTableRowCount('assets', 'id');
        $objectCount = $this->getTableRowCount('objects', 'o_id');
        $totalCount = $documentCount + $assetCount + $objectCount;

        if ($totalCount >= $this->criticalThreshold) {
            return new Failure(sprintf('Element count too high: %s', $totalCount), [
                'documents' => $documentCount,
                'assets' => $assetCount,
                'objects' => $objectCount,
                'total' => $totalCount,
            ]);
        }

        if ($totalCount >= $this->warningThreshold) {
            return new Warning(sprintf('Element count high: %s', $totalCount), [
                'documents' => $documentCount,
                'assets' => $assetCount,
                'objects' => $objectCount,
                'total' => $totalCount,
            ]);
        }

        return new Success(sprintf('There are %s Pimcore elements in the system', $totalCount), [
            'documents' => $documentCount,
            'assets' => $assetCount,
            'objects' => $objectCount,
            'total' => $totalCount,
        ]);
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
            $count = $qb->execute()->fetchOne();
        } catch (DBALException | DBALDriverException $e) {
            $count = 0;
        }

        return (int) $count;
    }
}
