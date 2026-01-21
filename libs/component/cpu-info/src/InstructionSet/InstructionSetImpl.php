<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo\InstructionSet;

use Boson\Component\CpuInfo\InstructionSetInterface;

/**
 * @phpstan-require-implements InstructionSetInterface
 */
trait InstructionSetImpl
{
    public function __construct(
        /**
         * @var non-empty-string
         */
        public readonly string $name,
    ) {}

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof InstructionSetInterface
                && $other->name === $this->name);
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function __debugInfo(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
