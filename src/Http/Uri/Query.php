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
     * @var array<non-empty-string, string|array<array-key, string>>
     */
    private array $components;

    /**
     * @param iterable<non-empty-string, string|array<array-key, string>> $components
     */
    public function __construct(iterable $components = [])
    {
        $this->components = \iterator_to_array($components);
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->components);
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $result = $this->components[$key] ?? $default;

        return match (true) {
            \is_string($result) => $result,
            \is_array($result) => (string) \reset($result),
            default => $default,
        };
    }

    public function int(string $key, ?int $default = null): ?int
    {
        $result = \filter_var($this->get($key), \FILTER_VALIDATE_INT);

        return $result === false ? $default : $result;
    }

    public function array(string $key, array $default = []): array
    {
        if (!\array_key_exists($key, $this->components)) {
            return $default;
        }

        $result = $this->components[$key] ?? [];

        return \is_array($result) ? $result : [$result];
    }

    public function toArray(): array
    {
        return $this->components;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->components as $key => $value) {
            if (\is_array($value)) {
                foreach ($value as $index => $item) {
                    yield \sprintf('%s[%s]', $key, $index) => $item;
                }

                continue;
            }

            yield $key => $value;
        }
    }

    public function count(): int
    {
        return \count($this->components);
    }
}
