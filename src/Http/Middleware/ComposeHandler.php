<?php

declare(strict_types=1);

namespace Boson\Http\Middleware;

use Boson\Http\RequestInterface;
use Boson\Http\ResponseInterface;

final readonly class ComposeHandler implements HandlerInterface
{
    public function __construct(
        private MiddlewareInterface $delegate,
        private HandlerInterface $next,
    ) {}

    public function handle(RequestInterface $request): ?ResponseInterface
    {
        return $this->delegate->handle($request, $this->next);
    }
}
