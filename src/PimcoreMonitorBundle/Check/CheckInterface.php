<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

interface CheckInterface
{
    /**
     * Return a unique ID describing this test instance.
     *
     * @return string
     */
    public function getIdentifier(): string;
}
