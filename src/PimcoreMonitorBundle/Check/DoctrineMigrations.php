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

use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Metadata\AvailableMigration;
use Doctrine\Migrations\Metadata\ExecutedMigration;
use Doctrine\Migrations\Version\Version;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;

class DoctrineMigrations extends AbstractCheck
{
    protected const IDENTIFIER = 'system:doctrine_migrations';

    /**
     * Type depends on the installed version of doctrine/migrations:
     * for ^2.0 it is string[], for ^3.0 it is Version[]
     *
     * @var Version[]|string[]
     */
    protected array $availableVersions;

    /**
     * Type depends on the installed version of doctrine/migrations:
     * for ^2.0 it is string[], for ^3.0 it is Version[]
     *
     * @var Version[]|string[]
     */
    protected array $migratedVersions;

    public function __construct(protected bool $skip, $input)
    {
        // check for doctrine/migrations:^3.0
        if ($input instanceof DependencyFactory) {
            $this->availableVersions = $this->getAvailableVersionsFromDependencyFactory($input);
            $this->migratedVersions = $this->getMigratedVersionsFromDependencyFactory($input);
            return;
        }

        // check for doctrine/migrations:^2.0
        if ($input instanceof Configuration &&
            \method_exists($input, 'getAvailableVersions') &&
            \method_exists($input, 'getMigratedVersions')
        ) {
            $this->availableVersions = $input->getAvailableVersions();
            $this->migratedVersions = $input->getMigratedVersions();
            return;
        }

        throw new \InvalidArgumentException(<<<'MESSAGE'
            Invalid Argument for DoctrineMigration check.
            If you are using doctrine/migrations ^3.0, pass Doctrine\Migrations\DependencyFactory as argument.
            If you are using doctrine/migrations ^2.0, pass Doctrine\Migrations\Configuration\Configuration as argument.
            MESSAGE
        );
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $notMigratedVersions = \array_diff($this->availableVersions, $this->migratedVersions);

        if (! empty($notMigratedVersions)) {
            return new Failure(
                'Not all migrations applied',
                \array_values(\array_map('strval', $notMigratedVersions))
            );
        }

        $notAvailableVersions = \array_diff($this->migratedVersions, $this->availableVersions);

        if (! empty($notAvailableVersions)) {
            return new Failure(
                'Migrations applied which are not available',
                \array_values(\array_map('strval', $notAvailableVersions))
            );
        }

        return new Success(
            'All migrations are correctly applied',
            \array_values(\array_map('strval', $this->migratedVersions))
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'Doctrine Migrations';
    }

    /**
     * @return Version[]
     */
    private function getAvailableVersionsFromDependencyFactory(DependencyFactory $dependencyFactory): array
    {
        $allMigrations = $dependencyFactory->getMigrationRepository()->getMigrations();

        return \array_map(
            static fn (AvailableMigration $availableMigration) => $availableMigration->getVersion(),
            $allMigrations->getItems()
        );
    }

    /**
     * @return Version[]
     */
    private function getMigratedVersionsFromDependencyFactory(DependencyFactory $dependencyFactory): array
    {
        $executedMigrations = $dependencyFactory->getMetadataStorage()->getExecutedMigrations();

        return \array_map(
            static fn (ExecutedMigration $executedMigration) => $executedMigration->getVersion(),
            $executedMigrations->getItems()
        );
    }
}
