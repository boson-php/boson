<?php

declare(strict_types=1);

namespace Boson\Internal;

use Boson\Contracts\Id\IntIdInterface;

/**
 * @template-implements IntIdInterface<int<0, max>>
 */
abstract readonly class PositiveIntId implements IntIdInterface
{
    final protected function __construct(
        /**
         * @var int<0, max>
         */
        protected int $id,
    ) {}

    /**
     * @param int<0, max> $id
     */
    public static function new(int $id): static
    {
        return new static($id);
    }

    public function toInteger(): int
    {
        return $this->id;
    }

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof self
                && $this->id === $other->id);
    }

    public function __debugInfo(): array
    {
        return [
            'id' => $this->id,
        ];
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return \sprintf('%s(%d)', static::class, $this->id);
    }
}
