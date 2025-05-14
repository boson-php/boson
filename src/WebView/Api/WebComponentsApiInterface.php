<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\WebComponentsApi\Exception\ComponentAlreadyDefinedException;
use Boson\WebView\Api\WebComponentsApi\Exception\WebComponentsApiException;

/**
 * Allows to register custom web components, check their existence,
 * and get their count.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements
 *
 * @template-extends \Traversable<non-empty-string, class-string>
 */
interface WebComponentsApiInterface extends \Traversable, \Countable
{
    /**
     * Registers a new component with the given tag name and component class.
     *
     * @param non-empty-string $name The component name (tag)
     * @param class-string $component The fully qualified class name of the component
     *
     * @throws ComponentAlreadyDefinedException If a component with the given name is already registered.
     * @throws WebComponentsApiException If any other registration error occurs.
     */
    public function add(string $name, string $component): void;

    /**
     * Checks if a component with the given name (tag) is registered.
     *
     * @param non-empty-string $name The component name (tag)
     *
     * @return bool Returns {@see true} if the component is
     *         registered, {@see false} otherwise.
     */
    public function has(string $name): bool;

    /**
     * Returns the number of registered components.
     *
     * @return int<0, max> The number of registered components (zero or greater).
     */
    public function count(): int;
}
