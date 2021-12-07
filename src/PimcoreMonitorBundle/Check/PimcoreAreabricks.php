<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Pimcore\Extension\Document\Areabrick\AreabrickManagerInterface;

class PimcoreAreabricks extends AbstractCheck
{
    protected const IDENTIFIER = 'pimcore:areabricks';

    protected AreabrickManagerInterface $areabrickManager;

    public function __construct(AreabrickManagerInterface $areabrickManager)
    {
        $this->areabrickManager = $areabrickManager;
    }

    public function check(): ResultInterface
    {
        $areabricks = [];

        foreach ($this->areabrickManager->getBricks() as $brickId => $brick) {
            $areabricks[] = [
                'identifier' => $brickId,
                'name' => $brick->getName(),
                'description' => $brick->getDescription(),
                'is_enabled' => $this->areabrickManager->isEnabled($brickId),
            ];
        }

        return new Success(sprintf('There are %s Areabricks in the system', \count($areabricks)), $areabricks);
    }

    public function getLabel(): string
    {
        return 'Pimcore Areabricks';
    }
}
