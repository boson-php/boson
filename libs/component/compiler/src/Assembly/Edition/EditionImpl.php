<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly\Edition;

use Boson\Component\Compiler\Assembly\EditionInterface;

/**
 * @phpstan-require-implements EditionInterface
 */
trait EditionImpl
{
    /**
     * @var list<non-empty-string>
     */
    public readonly array $extensions;

    /**
     * @param iterable<mixed, non-empty-string> $extensions
     */
    public function __construct(
        /**
         * @var non-empty-string
         */
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
