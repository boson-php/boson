<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

/**
 * @template-extends \Traversable<non-empty-string, scalar|null|array<array-key, mixed>>
 * @template-extends \ArrayAccess<non-empty-string, scalar|null|array<array-key, mixed>>
 */
interface QueryInterface extends
    UriComponentInterface,
    \Traversable,
    \ArrayAccess,
    \Countable
{
    /**
     * Returns {@see true} in case of passed key is defined in query
     * parameter or {@see false} instead.
     *
     * @param non-empty-string $name
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function has(string $name): bool;

    /**
     * Method for getting the query parameter value as a {@see scalar}
     * or {@see null}:
     * - Returns raw query value is {@see scalar} or {@see null}.
     * - Returns `$default` value in case of value is NOT defined or {@see array}.
     *
     * This behavior ensures correct serialization into a {@see string}.
     *
     * Note: To obtain an {@see array} value, please use ONLY the
     *       {@see QueryInterface::getAsArray()} method.
     *
     * @param non-empty-string $name
     * @param scalar|null $default
     * @return scalar|null
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function get(string $name, string|int|float|bool|null $default = null): string|int|float|bool|null;

    /**
     * Behavior is similar to the {@see get()} method.
     *
     * Returns an {@see string} if the URL/URI query parameter value is whole
     * {@see scalar} or {@see null}. Otherwise, returns the `$default` argument
     * or {@see null}.
     *
     * @param non-empty-string $name
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function getAsString(string $name, ?string $default = null): ?string;

    /**
     * Behavior is similar to the {@see get()} method.
     *
     * Returns an {@see int} if the URL/URI query parameter value is whole
     * {@see numeric}. Otherwise, returns the `$default` argument or {@see null}.
     *
     * @param non-empty-string $name
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function getAsInt(string $name, ?int $default = null): ?int;

    /**
     * Behavior is similar to the {@see get()} method.
     *
     * Returns a {@see bool} if the URL/URI query parameter value is whole
     * {@see scalar}. Otherwise, returns the `$default` argument or {@see null}.
     *
     * @param non-empty-string $name
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function getAsBool(string $name, ?bool $default = null): ?bool;

    /**
     * Returns all request parameters as an untyped array.
     *
     * @param non-empty-string $name
     * @param array<array-key, scalar|null|array<array-key, mixed>> $default
     *
     * @return array<array-key, scalar|null|array<array-key, mixed>>
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function getAsArray(string $name, array $default = []): array;

    /**
     * @return array<non-empty-string, scalar|null|array<array-key, mixed>>
     */
    public function toArray(): array;

    /**
     * Return an instance with the specified query parameters.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified query parameters.
     *
     * @param iterable<non-empty-string, scalar|null|iterable<array-key, mixed>> $parameters
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter is provided
     */
    public function withParameters(iterable $parameters): static;

    /**
     * Return an instance with an added query parameter with a specified name.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified query parameters.
     *
     * @param non-empty-string $name
     * @param scalar|null|iterable<array-key, mixed> $value
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter is provided
     */
    public function withParameter(string $name, string|int|float|bool|null|iterable $value): static;

    /**
     * Return an instance without a query parameter with a specified name.
     *
     * This method MUST retain the state of the current instance and return
     * an instance which excludes the query parameter with a specified name
     *
     * @param non-empty-string $name
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query name is provided
     */
    public function withoutParameter(string $name): static;

    /**
     * @return int<0, max>
     */
    public function count(): int;
}
