<?php

declare(strict_types=1);

namespace Boson\Http\Method;

use Boson\Http\MethodInterface;

interface MethodFactoryInterface
{
    /**
     * Performs a case-insensitive search from standard HTTP methods
     * or creates a new object if the method is non-standard.
     *
     * @param non-empty-string $method
     */
    public function createMethodFromString(string $method): MethodInterface;
}
