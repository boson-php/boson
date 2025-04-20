<?php

declare(strict_types=1);

namespace Boson\Shared\Id;

use Boson\Internal\Saucer\LibSaucer;
use Boson\Shared\ValueObject\ValueObjectInterface;
use FFI\CData;

/**
 * @template-implements IntIdInterface<int>
 */
abstract readonly class StructPointerId implements IntIdInterface
{
    protected function __construct(
        protected int $id,
        /**
         * UNSAFE pointer to the internal struct.
         *
         * @internal please don't use this field, for internal use only
         */
        public CData $ptr,
    ) {}

    /**
     * Returns {@see int} value from passed {@see CData} struct pointer
     *
     * @api
     */
    final protected static function getPointerIntValue(LibSaucer $api, CData $handle): int
    {
        // Cast any struct pointer (`<saucer_struct>*`)
        // to integer pointer (`intptr_t`) value.
        $id = $api->cast('intptr_t', $handle);

        /** @var int */
        return $id->cdata;
    }

    public function toInteger(): int
    {
        return $this->id;
    }

    /**
     * @return numeric-string&non-empty-string
     */
    public function toString(): string
    {
        return (string) $this->id;
    }

    public function equals(ValueObjectInterface $object): bool
    {
        return $object === $this || (
            $object instanceof self
                && $this->id === $object->id
        );
    }

    public function __serialize(): array
    {
        throw new \LogicException('Cannot serialize memory pointer ' . static::class);
    }

    public function __clone()
    {
        throw new \LogicException('Cannot clone memory pointer ' . static::class);
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
        return \sprintf('%s(%s)', static::class, $this->toString());
    }
}
