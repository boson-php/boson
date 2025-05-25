<?php

declare(strict_types=1);

namespace Boson\Component\Http\Static;

use Boson\Contracts\Http\RequestInterface;
use Boson\Contracts\Http\ResponseInterface;

interface StaticProviderInterface
{
    /**
     * Returns {@see ResponseInterface} in case of expected file
     * is present or {@see null} instead.
     */
    public function findFileByRequest(RequestInterface $request): ?ResponseInterface;
}
