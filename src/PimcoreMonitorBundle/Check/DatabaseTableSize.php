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
 * @copyright  2025 instride AG (https://instride.ch)
 * @license    https://github.com/instride-ch/pimcore-monitor/blob/main/gpl-3.0.txt GNU General Public License version 3 (GPLv3)
 */

namespace Instride\Bundle\PimcoreMonitorBundle\Check;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;

class DatabaseTableSize extends AbstractCheck
{
    protected const IDENTIFIER = 'device:database_table_size';

    public function __construct(
        protected bool       $skip,
        protected int        $warningThreshold,
        protected int        $criticalThreshold,
        protected array      $specialTables,
        protected Connection $connection
    )
    {
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $sizes = $this->getDatabaseTableSizes();

        if (!\is_array($sizes)) {
            return new Failure('Database table sizes could not be retrieved');
        }

        $data = [
            'ok' => 0,
            'warning' => [],
            'critical' => [],
        ];

        $hasSpecialTableConfig = false;
        if (!empty($this->specialTables) && \key_exists('configurations', $this->specialTables) && !empty($this->specialTables['configurations'])) {
            $hasSpecialTableConfig = true;
        }

        foreach ($sizes as $size) {
            $warningThreshold = $this->warningThreshold;
            $criticalThreshold = $this->criticalThreshold;

            $specialConfig = [];
            if ($hasSpecialTableConfig) {
                $specialConfig = \array_values(\array_filter($this->specialTables['configurations'], function ($item) use ($size) {
                    return isset($item['table_name']) && $item['table_name'] === $size['table'];
                }));
            }

            if (!empty($specialConfig)) {
                if (\count($specialConfig) > 1) {
                    return new Failure('Multiple special configs were retrieved for the same table name. Table -> ' . $size['table']);
                }

                $specialConfig = $specialConfig[0];
                if ($specialConfig['skip']) {
                    $data['skipped'] = $specialConfig['table_name'];
                    ++$data['ok'];
                    continue;
                }

                $warningThreshold = $specialConfig['warning_threshold'];
                $criticalThreshold = $specialConfig['critical_threshold'];
            }

            if ($size['size'] >= $criticalThreshold) {
                $data['critical'][$size['table']] = \formatBytes($size['size']);
                continue;
            }

            if ($size['size'] >= $warningThreshold) {
                $data['warning'][$size['table']] = \formatBytes($size['size']);
                continue;
            }

            ++$data['ok'];
        }

        if (\count($data['critical']) > 0) {
            return new Failure(
                \sprintf(
                    'Following database table sizes are too high: %s',
                    \implode(',', \array_keys($data['critical']))),
                $data
            );
        }

        if (\count($data['warning']) > 0) {
            return new Warning(
                \sprintf(
                    'Following database table sizes are high: %s',
                    \implode(',', \array_keys($data['warning']))),
                $data
            );
        }

        return new Success('All database table sizes are ok.', $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'Database Table Size';
    }

    /**
     * Returns the sizes of the connected database tables
     */
    private function getDatabaseTableSizes(): ?array
    {
        $query = "SELECT TABLE_NAME AS `table`,
                        (DATA_LENGTH + INDEX_LENGTH) AS `size`
                  FROM information_schema.TABLES
                  ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC;";

        try {
            return $this->connection->fetchAllAssociative($query);
        } catch (Exception) {
            return null;
        }
    }
}
