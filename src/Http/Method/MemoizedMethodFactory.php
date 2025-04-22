<?php

declare(strict_types=1);

namespace Boson\Http\Method;

use Boson\Http\MethodInterface;

final class MemoizedMethodFactory implements MethodFactoryInterface
{
    /**
     * @var array<non-empty-string, MethodInterface>
     *
     * @phpstan-var array<non-empty-uppercase-string, MethodInterface>
     */
    private array $methods = [];

    public function __construct(
        private readonly MethodFactoryInterface $delegate,
    ) {}

    public function createMethodFromString(string $method): MethodInterface
    {
        return $this->methods[\strtoupper($method)]
           ??= $this->delegate->createMethodFromString($method);
    }
}
