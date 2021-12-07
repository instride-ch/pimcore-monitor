<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface as BaseCheckInterface;

abstract class AbstractCheck implements BaseCheckInterface, CheckInterface
{
    /**
     * {@inheritDoc}
     */
    public function getIdentifier(): string
    {
        return static::IDENTIFIER;
    }
}
