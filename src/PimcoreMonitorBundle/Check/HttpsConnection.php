<?php

namespace Wvision\Bundle\PimcoreMonitorBundle\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class HttpsConnection extends AbstractCheck
{
    protected const IDENTIFIER = 'system:https_connection';

    protected bool $skip;
    protected ?Request $request;

    public function __construct(bool $skip, RequestStack $requestStack)
    {
        $this->skip = $skip;
        $this->request = $requestStack->getMainRequest() ?: $requestStack->getCurrentRequest();
    }

    /**
     * {@inheritDoc}
     */
    public function check(): ResultInterface
    {
        if ($this->skip) {
            return new Skip('Check was skipped');
        }

        if (null === $this->request || ! $this->request->isSecure()) {
            return new Failure('HTTPS encryption not activated');
        }

        return new Success('HTTPS encryption activated');
    }

    /**
     * {@inheritDoc}
     */
    public function getLabel(): string
    {
        return 'HTTPS Connection';
    }
}
