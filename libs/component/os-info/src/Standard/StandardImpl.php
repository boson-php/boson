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
        public readonly ?self $parent = null,
    ) {}

    public function isSupports(StandardInterface $standard): bool
    {
        return $this === $standard || $this->parent?->isSupports($standard) === true;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
