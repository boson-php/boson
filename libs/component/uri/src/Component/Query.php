<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Contracts\Uri\Component\QueryInterface;

/**
 * @template-implements \IteratorAggregate<non-empty-string, string>
 */
final class Query implements QueryInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-string, string|array<array-key, string>>
     */
    private array $parameters;

    /**
     * @param iterable<non-empty-string, string|array<array-key, string>> $parameters
     */
    public function __construct(iterable $parameters = [])
    {
        $this->parameters = \iterator_to_array($parameters);
    }

    public function has(string $key): bool
    {
        return \array_key_exists($key, $this->parameters);
    }

    public function get(string $key, ?string $default = null): ?string
    {
        $result = $this->parameters[$key] ?? $default;

        return match (true) {
            \is_string($result) => $result,
            \is_array($result) => (string) \reset($result),
            default => $default,
        };
    }

    public function getAsInt(string $key, ?int $default = null): ?int
    {
        $result = \filter_var($this->get($key), \FILTER_VALIDATE_INT);

        return $result === false ? $default : $result;
    }

    public function getAsArray(string $key, array $default = []): array
    {
        if (!\array_key_exists($key, $this->parameters)) {
            return $default;
        }

        $result = $this->parameters[$key] ?? [];

        return \is_array($result) ? $result : [$result];
    }

    public function toArray(): array
    {
        return $this->parameters;
    }

    public function getIterator(): \Traversable
    {
        foreach ($this->parameters as $key => $value) {
            // Note: This behaviour is specific for PHP environment only;
            //       implementations in other languages may not interpret
            //       this construct correctly.
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
        return \count($this->parameters);
    }

    public function equals(mixed $other): bool
    {
        return $other === $this
            || ($other instanceof self
                && $other->parameters === $this->parameters);
    }

    public function toString(): string
    {
        return (string) $this;
    }

    public function __toString(): string
    {
        $result = [];

        foreach ($this as $key => $value) {
            /** @phpstan-ignore-next-line PHPStan false-positive. PHP may contain integer keys in array */
            $result[] = \rawurlencode((string) $key)
                . '='
                . \rawurlencode($value);
        }

        return \implode('&', $result);
    }
}
