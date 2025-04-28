<?php

declare(strict_types=1);

namespace Boson\Bridge\Static;

use Boson\Http\RequestInterface;
use Boson\Http\ResponseInterface;

interface StaticAdapterInterface
{
    /**
     * Returns {@see ResponseInterface} in case of expected file
     * is present or {@see null} instead.
     */
    public function lookup(RequestInterface $request): ?ResponseInterface;
}
