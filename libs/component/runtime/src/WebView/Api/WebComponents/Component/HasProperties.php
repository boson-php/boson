<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

use Boson\WebView\Api\WebComponents\Context\MutablePropertyMapInterface;

/**
 * @phpstan-require-implements HasPropertiesInterface
 */
trait HasProperties
{
    public readonly MutablePropertyMapInterface $properties;

    /**
     * @api
     *
     * @uses PropertyMapInterface::has()
     *
     * @param non-empty-string $name
     */
    public function hasProperty(string $name): bool
    {
        return $this->properties->has($name);
    }

    /**
     * @api
     *
     * @uses PropertyMapInterface::remove()
     *
     * @param non-empty-string $name
     */
    public function removeProperty(string $name): void
    {
        $this->properties->remove($name);
    }

    /**
     * @api
     *
     * @uses PropertyMapInterface::get()
     *
     * @param non-empty-string $name
     */
    public function getProperty(string $name): mixed
    {
        return $this->properties->get($name);
    }

    /**
     * @api
     *
     * @uses PropertyMapInterface::set()
     *
     * @param non-empty-string $name
     */
    public function setProperty(string $name, mixed $value): void
    {
        $this->properties->set($name, $value);
    }
}
