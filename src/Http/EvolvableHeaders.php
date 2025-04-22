<?php

declare(strict_types=1);

namespace Boson\Http;

/**
 * An implementation of evolvable (immutable with the ability to change
 * data in new instances) headers list.
 */
class EvolvableHeaders extends Headers implements EvolvableHeadersInterface
{
    public function withHeader(string $name, string $value): self
    {
        $self = clone $this;
        /** @phpstan-ignore-next-line : False-positive, does not mutate this context */
        $self->lines[self::normalizeHeaderName($name)] = [$value];

        return $self;
    }

    public function withAddedHeader(string $name, string $value): self
    {
        $self = clone $this;
        /** @phpstan-ignore-next-line : False-positive, does not mutate this context */
        $self->lines[self::normalizeHeaderName($name)][] = $value;

        return $self;
    }

    public function withoutHeader(string $name): self
    {
        $self = clone $this;
        /** @phpstan-ignore-next-line : False-positive, does not mutate this context */
        unset($self->lines[self::normalizeHeaderName($name)]);

        return $self;
    }

    public function withoutHeaders(): self
    {
        $self = clone $this;
        /** @phpstan-ignore-next-line : False-positive, does not mutate this context */
        $self->lines = [];

        return $self;
    }
}
