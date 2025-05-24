<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents;

/**
 * @template-extends \Traversable<non-empty-string, class-string>
 */
interface WebComponentsMapInterface extends \Traversable, \Countable
{
    /**
     * Checks if a component with the given name (tag) is registered.
     *
     * @param non-empty-string $name The component name (tag)
     *
     * @return bool returns {@see true} if the component is
     *         registered, {@see false} otherwise
     */
    public function has(string $name): bool;

    /**
     * Returns the number of registered components.
     *
     * @return int<0, max> the number of registered components (zero or greater)
     */
    public function count(): int;
}
