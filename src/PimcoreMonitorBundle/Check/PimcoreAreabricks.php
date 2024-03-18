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

use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Extension\Document\Areabrick\AreabrickManagerInterface;

class PimcoreAreabricks extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:areabricks';

    public function __construct(protected bool $skip, protected AreabrickManagerInterface $areabrickManager) {}

    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        $bricks = [];

        foreach ($this->areabrickManager->getBricks() as $brickId => $brick) {
            $bricks[] = [
                'identifier' => $brickId,
                'name' => $brick->getName(),
                'description' => $brick->getDescription(),
                'is_enabled' => $this->areabrickManager->isEnabled($brickId),
            ];
        }

        return new Success(\sprintf('There are %s Areabricks in the system', \count($bricks)), $bricks);
    }

    public function getLabel(): string
    {
        return 'Pimcore Areabricks';
    }
}
