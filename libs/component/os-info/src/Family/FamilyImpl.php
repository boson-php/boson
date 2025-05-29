<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo\Family;

use Boson\Component\OsInfo\FamilyInterface;

/**
 * @phpstan-require-implements FamilyInterface
 */
trait FamilyImpl
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $name,
        public readonly ?FamilyInterface $parent = null,
    ) {}

    public function is(FamilyInterface $family): bool
    {
        return $this === $family || $this->parent?->is($family) === true;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
