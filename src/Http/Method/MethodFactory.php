<?php

declare(strict_types=1);

namespace Boson\Http\Method;

use Boson\Http\Method;
use Boson\Http\MethodInterface;

final readonly class MethodFactory implements MethodFactoryInterface
{
    public function createMethodFromString(string $method): MethodInterface
    {
        return Method::tryFrom(\strtoupper($method))
            ?? $this->createUserDefinedMethod($method);
    }

    /**
     * @param non-empty-string $method
     */
    private function createUserDefinedMethod(string $method): Method
    {
        return new Method($method);
    }
}
