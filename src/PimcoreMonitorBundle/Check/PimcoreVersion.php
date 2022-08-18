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

use Composer\InstalledVersions;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;

class PimcoreVersion extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:version';

    protected bool $skip;

    public function __construct(bool $skip)
    {
        $this->skip = $skip;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $version = InstalledVersions::getPrettyVersion('pimcore/pimcore');

        return new Success(sprintf('The system is running on Pimcore %s', $version), [
            'semver' => $version,
            'reference' => InstalledVersions::getReference('pimcore/pimcore'),
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
