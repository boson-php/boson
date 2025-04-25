<?php

declare(strict_types=1);

namespace Boson\Http;

/**
 * An implementation of immutable headers list.
 *
 * @template-implements \IteratorAggregate<non-empty-lowercase-string, \Stringable|string>
 */
class Headers implements HeadersInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-lowercase-string, list<\Stringable|string>>
     */
    protected array $lines;

    /**
     * @param iterable<non-empty-string, \Stringable|string> $headers
     */
    public function __construct(iterable $headers = [])
    {
        $this->lines = self::packHeaderLines($headers);
    }

    /**
     * @phpstan-pure
     *
     * @param non-empty-string $name
     *
     * @return non-empty-lowercase-string
     */
    protected static function normalizeHeaderName(string $name): string
    {
        return \strtolower($name);
    }

    /**
     * @param iterable<non-empty-string, \Stringable|string> $headers
     *
     * @return array<non-empty-lowercase-string, list<\Stringable|string>>
     */
    private static function packHeaderLines(iterable $headers): array
    {
        $result = [];

        foreach ($headers as $name => $value) {
            $result[self::normalizeHeaderName($name)][] = $value;
        }

        return $result;
    }

    public function first(string $name, \Stringable|string|null $default = null): \Stringable|string|null
    {
        $headerLines = $this->lines[self::normalizeHeaderName($name)] ?? [];

        foreach ($headerLines as $headerLine) {
            return $headerLine;
        }

        return $default;
    }

    public function contains(string $name): bool
    {
        return ($this->lines[self::normalizeHeaderName($name)] ?? []) !== [];
    }

    public function offsetExists(mixed $offset): bool
    {
        assert(\is_string($offset) && $offset !== '');

        return isset($this->lines[$offset]);
    }

    /**
     * @return list<\Stringable|string>
     */
    public function offsetGet(mixed $offset): array
    {
        assert(\is_string($offset) && $offset !== '');

        /** @var list<\Stringable|string> */
        return $this->lines[$offset] ?? [];
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
        foreach ($this->lines as $key => $values) {
            foreach ($values as $value) {
                yield $key => $value;
            }
        }
    }

    public function count(): int
    {
        return \count($this->lines);
    }
}
