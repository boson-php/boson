<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidQueryNameArgumentException;
use Boson\Component\Uri\Exception\InvalidQueryValueArgumentException;
use Boson\Contracts\Uri\Component\QueryInterface;

/**
 * @template-implements \IteratorAggregate<non-empty-string, string>
 *
 * @phpstan-consistent-constructor
 */
class Query implements QueryInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-string, string|array<array-key, string>>
     */
    protected array $parameters;

    /**
     * @param iterable<non-empty-string, string|iterable<array-key, string>> $parameters
     *
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    public function __construct(iterable $parameters = [])
    {
        $this->parameters = $this->formatParameters($parameters);
    }

    /**
     * Returns a query instance from another one
     *
     * @api
     *
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    final public static function from(QueryInterface $query): static
    {
        if ($query instanceof static) {
            return clone $query;
        }

        return new static(
            parameters: $query->toArray(),
        );
    }

    /**
     * Returns a query instance from another one
     *
     * @api
     *
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    final public static function tryFrom(?QueryInterface $query): ?static
    {
        if ($query === null) {
            return null;
        }

        return static::from($query);
    }

    public function has(string $name): bool
    {
        return \array_key_exists($name, $this->parameters);
    }

    public function get(string $name, ?string $default = null): ?string
    {
        $result = $this->parameters[$name] ?? $default;

        return match (true) {
            \is_string($result) => $result,
            \is_array($result) => (string) \reset($result),
            default => $default,
        };
    }

    public function getAsInt(string $name, ?int $default = null): ?int
    {
        $result = \filter_var($this->get($name), \FILTER_VALIDATE_INT);

        return $result === false ? $default : $result;
    }

    public function getAsArray(string $name, array $default = []): array
    {
        if (!\array_key_exists($name, $this->parameters)) {
            return $default;
        }

        $result = $this->parameters[$name] ?? [];

        return \is_array($result) ? $result : [$result];
    }

    public function toArray(): array
    {
        return $this->parameters;
    }

    /**
     * @param iterable<non-empty-string, string|iterable<array-key, string>> $parameters
     *
     * @return array<non-empty-string, string|array<array-key, string>>
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    protected function formatParameters(iterable $parameters): array
    {
        $result = [];

        foreach ($parameters as $name => $value) {
            $result[$this->formatParameterName($name)] = $this->formatParameterValue($value);
        }

        return $result;
    }

    /**
     * @return non-empty-string
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    protected function formatParameterName(string $name): string
    {
        if ($name === '') {
            throw InvalidQueryNameArgumentException::becauseComponentIsEmpty();
        }

        return $name;
    }

    /**
     * @param string|iterable<array-key, string> $value
     *
     * @return string|array<array-key, string>
     *
     * @phpstan-return ($value is string ? string : array<array-key, string>)
     *
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    protected function formatParameterValue(string|iterable $value): string|array
    {
        if (\is_string($value)) {
            return $value;
        }

        $result = [];

        foreach ($value as $key => $val) {
            if (!\is_int($key) && !\is_string($key)) {
                throw InvalidQueryValueArgumentException::becauseComponentMustBe('array-key', $key);
            }

            if (!\is_string($val)) {
                throw InvalidQueryValueArgumentException::becauseComponentMustBe('string', $val);
            }

            $result[$key] = $val;
        }

        return $result;
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    final public function offsetExists(mixed $offset): bool
    {
        if (!\is_string($offset) || $offset === '') {
            throw InvalidQueryNameArgumentException::becauseComponentMustBe('non-empty-string', $offset);
        }

        return isset($this->parameters[$offset]);
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    public function offsetGet(mixed $offset): string|array|null
    {
        if (!\is_string($offset) || $offset === '') {
            throw InvalidQueryNameArgumentException::becauseComponentMustBe('non-empty-string', $offset);
        }

        return $this->parameters[$offset] ?? null;
    }

    #[\Deprecated('Data mutation in an immutable context is not allowed')]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException('Cannot modify value of immutable path ' . static::class);
    }

    #[\Deprecated('Data mutation in an immutable context is not allowed')]
    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException('Cannot remove value of immutable path ' . static::class);
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
                && $other->parameters === $this->parameters)
            || ($other instanceof QueryInterface
                && $other->toArray() === $this->parameters);
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
