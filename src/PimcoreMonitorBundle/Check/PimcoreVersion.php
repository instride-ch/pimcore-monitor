<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use PackageVersions\Versions;

class PimcoreVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:version';
    protected const PART_SEMVER = 0;
    protected const PART_REFERENCE = 1;

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        $version = explode('@', Versions::getVersion('pimcore/pimcore'));

        return new Success(sprintf('The system is running on Pimcore %s', $version[self::PART_SEMVER]), [
            'semver' => $version[self::PART_SEMVER],
            'reference' => $version[self::PART_REFERENCE],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'Pimcore Version';
    }
}
