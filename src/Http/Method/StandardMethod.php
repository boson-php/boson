<?php

declare(strict_types=1);

namespace Boson\Http\Method;

use Boson\Http\MethodInterface;

/**
 * An implementation of the builtin method.
 */
abstract readonly class StandardMethod implements MethodInterface
{
    use MethodValueObjectImpl;

    /**
     * @var non-empty-string
     *
     * @phpstan-var non-empty-uppercase-string
     */
    public string $name;

    /**
     * @param non-empty-string $name
     */
    public function __construct(
        string $name,
        public bool $isIdempotent = false,
        public bool $isSafe = false,
    ) {
        $this->name = \strtoupper($name);
    }
}
