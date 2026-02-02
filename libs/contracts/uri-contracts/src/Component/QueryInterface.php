<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

/**
 * @template-extends \Traversable<non-empty-string, string>
 * @template-extends \ArrayAccess<non-empty-string, string|array<array-key, string>>
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
     */
    public function get(string $name, ?string $default = null): ?string;

    /**
     * Behavior is similar to the {@see get()} method.
     *
     * Returns an {@see int} if the URL/URI query parameter value is whole numeric.
     * Otherwise, returns the `$default` argument or {@see null}.
     *
     * @param non-empty-string $name
     */
    public function getAsInt(string $name, ?int $default = null): ?int;

    /**
     * Returns all request parameters as an array.
     *
     * @param non-empty-string $name
     * @param array<array-key, string> $default
     *
     * @return array<array-key, string>
     */
    public function getAsArray(string $name, array $default = []): array;

    /**
     * @return array<non-empty-string, string|array<array-key, string>>
     */
    public function toArray(): array;
}
