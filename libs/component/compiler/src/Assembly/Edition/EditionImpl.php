<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly\Edition;

use Boson\Component\Compiler\Assembly\EditionInterface;

/**
 * @phpstan-require-implements EditionInterface
 */
trait EditionImpl
{
    public readonly array $extensions;

    public function __construct(
        public readonly string $name,
        iterable $extensions = [],
    ) {
        $this->extensions = \iterator_to_array($extensions, false);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
