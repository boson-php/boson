<?php

declare(strict_types=1);

namespace Boson\Contracts\Uri\Component;

use Boson\Contracts\Uri\Exception\InvalidArgumentExceptionInterface;

interface MutableQueryInterface extends QueryInterface
{
    /**
     * Replaces all query parameters with the provided iterable collection.
     *
     * This method clears all existing query parameters and replaces them with
     * the provided parameters. The operation is atomic - either all parameters
     * are successfully set or the original state is preserved.
     *
     * Parameter values can be:
     *
     * - A {@see string} scalar type:
     *   ```
     *   $query->putAll(['page' => 1, 'sort' => 'name']);
     *   // possible result: "page=1&sort=name"
     *   ```
     *
     * - Iterables for array parameters (e.g., ["tag1", "tag2"]):
     *   ```
     *   $query->putAll(['tags' => ['php', 'oop', 'uri']]);
     *   // possible result: "tags[]=php&tags[]=oop&tags[]=uri"
     *   ```
     *
     * - An {@see iterable} type:
     *   ```
     *   $query->putAll([
     *      'search' => 'example',
     *      'filters' => ['type' => 'user', 'status' => 'active']
     *   ]);
     *   // possible result: "search=example&filters[type]=user&filters[status]=active"
     *   ```
     *
     * Note: The "possible result" in examples depends on the rendering
     *       implementation (i.e. RFC 3986, RFC 1738, WhatWG, etc.).
     *
     * @param iterable<non-empty-string, string|iterable<array-key, string|iterable<array-key, mixed>>> $parameters
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path segment is provided
     */
    public function putAll(iterable $parameters): void;

    /**
     * Sets or replaces a single query parameter.
     *
     * If the parameter name already exists, its value is replaced.
     *
     * Parameter values can be:
     *
     * - A {@see string} scalar type:
     *   ```
     *   $query->put('page', 2);
     *   // possible result: "page=2"
     *   ```
     *
     * - An {@see iterable} type:
     *   ```
     *   $query->put('colors', ['red', 'green', 'blue']);
     *   // possible result: "colors[]=red&colors[]=green&colors[]=blue"
     *   ```
     *
     * Note: The "possible result" in examples depends on the rendering
     *        implementation (i.e. RFC 3986, RFC 1738, WhatWG, etc.).
     *
     * @param non-empty-string $name
     * @param string|iterable<array-key, string|iterable<array-key, mixed>> $value
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path segment or index is provided
     */
    public function put(string $name, string|iterable $value): void;

    /**
     * Removes a query parameter by name.
     *
     * Removes the specified parameter from the query string. If the parameter
     * does not exist, the method completes silently (no exception thrown).
     *
     * ```php
     * // Before: "page=1&sort=name&filters[type]=user"
     *
     * $query->remove('sort');
     * // possible result: "page=1&filters[type]=user"
     *
     * $query->remove('filters');
     * // possible result: "page=1"
     * ```
     *
     * Note: The "possible result" in examples depends on the rendering
     *       implementation (i.e. RFC 3986, RFC 1738, WhatWG, etc.).
     *
     * @param non-empty-string $name
     *
     * @throws InvalidArgumentExceptionInterface if an invalid path index is provided
     */
    public function remove(string $name): void;
}
