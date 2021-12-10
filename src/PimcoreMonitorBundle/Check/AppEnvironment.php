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

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;

class AppEnvironment extends AbstractCheck
{
    protected const IDENTIFIER = 'system:app_environment';

    protected bool $skip;
    protected string $environment;

    public function __construct(bool $skip, string $environment)
    {
        $this->skip = $skip;
        $this->environment = $environment;
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        if ('prod' !== $this->environment) {
            return new Failure('Application is not running in production mode', $this->environment);
        }

        return new Success('Application is running in production mode', $this->environment);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'App Environment';
    }
}
