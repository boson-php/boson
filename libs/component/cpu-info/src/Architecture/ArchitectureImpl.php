<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\Architecture;

use Boson\Component\CpuInfo\ArchitectureInterface;

/**
 * @phpstan-require-implements ArchitectureInterface
 */
trait ArchitectureImpl
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $name,
        public readonly ?ArchitectureInterface $parent = null,
    ) {}

    public function is(ArchitectureInterface $architecture): bool
    {
        return $this === $architecture
            || $this->parent?->is($architecture) === true;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
