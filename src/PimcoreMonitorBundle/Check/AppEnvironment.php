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

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;

class AppEnvironment extends AbstractCheck
{
    protected const IDENTIFIER = 'system:app_environment';

    public function __construct(protected bool $skip, protected string $environment) {}

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
