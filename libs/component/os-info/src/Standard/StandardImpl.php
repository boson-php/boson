<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Standard;

use Boson\Component\OsInfo\StandardInterface;

/**
 * @phpstan-require-implements StandardInterface
 */
trait StandardImpl
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $name,
        public readonly ?StandardInterface $parent = null,
    ) {}

    public function is(StandardInterface $standard): bool
    {
        return $this === $standard || $this->parent?->is($standard) === true;
    }

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof StandardInterface
                && $other->name === $this->name);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function __debugInfo(): array
    {
        return [
            'name' => $this->name,
            'parent' => $this->parent,
        ];
    }
}
