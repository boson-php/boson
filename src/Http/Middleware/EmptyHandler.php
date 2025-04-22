<?php

declare(strict_types=1);

namespace Boson\Http\Middleware;

use Boson\Http\RequestInterface;
use Boson\Http\ResponseInterface;

final readonly class EmptyHandler implements HandlerInterface
{
    public function handle(RequestInterface $request): ?ResponseInterface
    {
        return null;
    }
}
