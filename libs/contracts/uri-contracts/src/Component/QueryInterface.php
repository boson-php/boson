<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

/**
 * @template-extends \Traversable<non-empty-string, string>
 * @template-extends \ArrayAccess<non-empty-string, string|array<array-key, string|array<array-key, mixed>>>
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
     * Returns raw query parameter if defined or default value if query
     * parameter has not been passed.
     *
     * If the URL contains an array of string query parameters, the method
     * returns the first element.
     *
     * If the URL contains one query parameter, it is returned as a string
     * (URL can only contain strings).
     *
     * If there is no such URL/URI query parameter, the `$default` argument
     * or {@see null} will be returned.
     *
     * @param non-empty-string $name
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function get(string $name, ?string $default = null): ?string;

    /**
     * Behavior is similar to the {@see get()} method.
     *
     * Returns an {@see int} if the URL/URI query parameter value is whole numeric.
     * Otherwise, returns the `$default` argument or {@see null}.
     *
     * @param non-empty-string $name
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function getAsInt(string $name, ?int $default = null): ?int;

    /**
     * Returns all request parameters as an array.
     *
     * @param non-empty-string $name
     * @param array<array-key, string> $default
     *
     * @return array<array-key, string>
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter name is provided
     */
    public function getAsArray(string $name, array $default = []): array;

    /**
     * @return array<non-empty-string, string|array<array-key, string|array<array-key, mixed>>>
     */
    public function toArray(): array;

    /**
     * Return an instance with the specified query parameters.
     *
     * This method MUST retain the state of the current instance and return
     * an instance that contains the specified query parameters.
     *
     * @param iterable<non-empty-string, string|iterable<array-key, string|iterable<array-key, mixed>>> $parameters
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
     * @param string|iterable<array-key, string|iterable<array-key, mixed>> $value
     *
     * @throws InvalidArgumentExceptionInterface if an invalid query parameter is provided
     */
    public function withParameter(string $name, string|iterable $value): static;

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
