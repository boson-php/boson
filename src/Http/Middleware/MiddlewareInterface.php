<?php

declare(strict_types=1);

namespace Boson\Http\Middleware;

use Boson\Http\RequestInterface;
use Boson\Http\ResponseInterface;

interface MiddlewareInterface
{
    public function handle(RequestInterface $request, HandlerInterface $handler): ?ResponseInterface;
}
