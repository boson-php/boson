<?php

declare(strict_types=1);

namespace Boson\Component\Uri\Component;

use Boson\Component\Uri\Exception\InvalidQueryNameArgumentException;
use Boson\Component\Uri\Exception\InvalidQueryValueArgumentException;
use Boson\Contracts\Uri\Component\QueryInterface;
use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

/**
 * @template-implements \IteratorAggregate<non-empty-string, string|null|array<array-key, mixed>>
 *
 * @phpstan-sealed MutableQuery
 *
 * @phpstan-consistent-constructor
 */
class Query implements QueryInterface, \IteratorAggregate
{
    /**
     * @var array<non-empty-string, string|null|array<array-key, mixed>>
     */
    protected array $parameters;

    /**
     * @param iterable<mixed, mixed> $parameters
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

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    public function has(string $name): bool
    {
        $formattedName = $this->formatParameterName($name);

        return \array_key_exists($formattedName, $this->parameters);
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    public function get(string $name, ?string $default = null): ?string
    {
        $formattedName = $this->formatParameterName($name);

        if (\array_key_exists($formattedName, $this->parameters)) {
            $value = $this->parameters[$formattedName];

            if (\is_scalar($value) || $value === null) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidArgumentExceptionInterface in case of other validation errors
     */
    public function getAsInt(string $name, ?int $default = null): ?int
    {
        $result = \filter_var($this->get($name), \FILTER_VALIDATE_INT);

        return $result === false ? $default : $result;
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidArgumentExceptionInterface in case of other validation errors
     */
    public function getAsBool(string $name, ?bool $default = null): ?bool
    {
        if ($this->has($name)) {
            return \filter_var($this->get($name), \FILTER_VALIDATE_BOOL);
        }

        return $default;
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    public function getAsArray(string $name, array $default = []): array
    {
        $formattedName = $this->formatParameterName($name);

        if (!\array_key_exists($formattedName, $this->parameters)) {
            return $default;
        }

        $result = $this->parameters[$formattedName] ?? [];

        return \is_array($result) ? $result : [$result];
    }

    public function toArray(): array
    {
        return $this->parameters;
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     */
    protected function removeParameter(string $name): void
    {
        unset($this->parameters[$this->formatParameterName($name)]);
    }

    /**
     * @throws InvalidQueryNameArgumentException if an invalid query name is provided
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    protected function setParameter(string $name, mixed $value): void
    {
        $this->parameters[$this->formatParameterName($name)]
            = $this->formatParameterValue($value);
    }

    /**
     * @param iterable<mixed, mixed> $parameters
     *
     * @return array<non-empty-string, string|null|array<array-key, mixed>>
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
     * @return string|null|array<array-key, mixed>
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    protected function formatParameterValue(mixed $value): string|null|array
    {
        return match (true) {
            \is_string($value),
            $value === null => $value,
            \is_scalar($value) => \var_export($value, true),
            \is_iterable($value) => $this->formatParameterIterableValue($value),
            default => throw InvalidQueryValueArgumentException::becauseComponentMustBe(
                expected: 'scalar|null|iterable<array-key, mixed>',
                given: $value,
            ),
        };
    }

    /**
     * @param iterable<mixed, mixed> $value
     *
     * @return array<array-key, string|null|array<array-key, mixed>>
     * @throws InvalidQueryValueArgumentException if an invalid query value is provided
     */
    protected function formatParameterIterableValue(iterable $value): array
    {
        $result = [];

        foreach ($value as $key => $val) {
            if (!\is_int($key) && !\is_string($key)) {
                throw InvalidQueryValueArgumentException::becauseComponentMustBe('array-key', $key);
            }

            $result[$key] = $this->formatParameterValue($val);
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
        return new \ArrayIterator($this->parameters);
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
        return \http_build_query($this->parameters);
    }
}
