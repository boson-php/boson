<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\WebView\Api\WebComponentsApi\Exception\ComponentAlreadyDefinedException;
use Boson\WebView\Api\WebComponentsApi\Exception\InvalidComponentNameException;

/**
 * @template-extends \Traversable<non-empty-string, class-string>
 */
interface WebComponentsApiInterface extends \Traversable, \Countable
{
    /**
     * @param non-empty-string $name
     * @param class-string $component
     *
     * @throws InvalidComponentNameException in case the component name is incorrect
     * @throws ComponentAlreadyDefinedException in case the component has already been registered
     */
    public function add(string $name, string $component): void;

    /**
     * Returns {@see true} in case of the component with given tag
     * name is registered.
     *
     * @param non-empty-string $name
     */
    public function has(string $name): bool;

    /**
     * @return int<0, max>
     */
    public function count(): int;
}
