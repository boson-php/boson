<?php

declare(strict_types=1);

namespace Boson\Http\Uri;

use Boson\Http\Uri\Query\QueryValueObjectImpl;

/**
 * @template-implements \IteratorAggregate<non-empty-string, string>
 */
final class Query implements QueryInterface, \IteratorAggregate
{
    use QueryValueObjectImpl;

    /**
     * @var array<non-empty-string, list<string>>
     */
    private array $components;

    /**
     * @param iterable<non-empty-string, string> $components
     */
    public function __construct(iterable $components = [])
    {
        $this->components = self::packComponents($components);
    }

    /**
     * @template TArgKey of non-empty-string
     * @template TArgValue of string
     *
     * @param iterable<TArgKey, TArgValue> $components
     *
     * @return array<TArgKey, list<TArgValue>>
     */
    private static function packComponents(iterable $components): array
    {
        $result = [];

        foreach ($components as $key => $value) {
            $result[$key][] = $value;
        }

        return $result;
    }

    public function offsetExists(mixed $offset): bool
    {
        assert(\is_string($offset) && $offset !== '');

        return isset($this->components[$offset]);
    }

    /**
     * @return list<string>
     */
    public function offsetGet(mixed $offset): array
    {
        assert(\is_string($offset) && $offset !== '');

        /** @var list<string> */
        return $this->components[$offset] ?? [];
    }

    /**
     * @internal
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \OutOfRangeException(self::class . ' is immutable');
    }

    /**
     * @internal
     */
    public function offsetUnset(mixed $offset): void
    {
        throw new \OutOfRangeException(self::class . ' is immutable');
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->components as $key => $values) {
            foreach ($values as $value) {
                yield $key => $value;
            }
        }
    }

    public function count(): int
    {
        return \count($this->components);
    }
}
