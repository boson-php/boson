<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents;

use Boson\WebView\Api\WebComponents\Exception\ComponentAlreadyDefinedException;
use Boson\WebView\Api\WebComponents\Exception\WebComponentsApiException;

interface WebComponentsRegistrarInterface
{
    /**
     * Registers a new component with the given tag name and component class.
     *
     * @param non-empty-string $name The component name (tag)
     * @param class-string $component The fully qualified class name of the component
     *
     * @throws ComponentAlreadyDefinedException if a component with the given name is already registered
     * @throws WebComponentsApiException if any other registration error occurs
     */
    public function add(string $name, string $component): void;
}
