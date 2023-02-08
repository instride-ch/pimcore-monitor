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
use Laminas\Diagnostics\Result\Warning;

class HttpsConnection extends AbstractCheck
{
    protected const IDENTIFIER = 'system:https_connection';

    public function __construct(protected bool $skip, protected array $systemConfig) {}

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $host = $this->systemConfig['general']['domain'] ?? null;

        if (null === $host) {
            return new Warning('HTTPS encryption could not be checked');
        }

        // Create a stream context
        $stream = \stream_context_create(['ssl' => ['capture_peer_cert' => true]]);
        $url = \sprintf('https://%s', $host);

        try {
            // Bind the resource $url to $stream
            $read = \fopen($url, 'rb', false, $stream);

            // Get the stream parameters
            $params = \stream_context_get_params($read);
        } catch (\Exception) {
            // Ignore exceptions thrown ...
        }

        // Check if SSL certificate is present
        $cert = $params['options']['ssl']['peer_certificate'] ?? null;

        if (null === $cert) {
            return new Failure('HTTPS encryption not activated', false);
        }

        return new Success('HTTPS encryption activated', true);
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'HTTPS Connection';
    }
}
