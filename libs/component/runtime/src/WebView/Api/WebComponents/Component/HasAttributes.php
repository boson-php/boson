<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Component;

use Boson\WebView\Api\WebComponents\Context\MutableAttributeMapInterface;

/**
 * @phpstan-require-implements HasAttributesInterface
 */
trait HasAttributes
{
    public readonly MutableAttributeMapInterface $attributes;

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/hasAttribute
     *
     * @uses AttributeMapInterface::has()
     *
     * @param non-empty-string $name
     */
    public function hasAttribute(string $name): bool
    {
        return $this->attributes->has($name);
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/hasAttributes
     *
     * @uses AttributeMapInterface::count()
     */
    public function hasAttributes(): bool
    {
        return $this->attributes->count() > 0;
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/removeAttribute
     *
     * @uses MutableAttributeMapInterface::remove()
     *
     * @param non-empty-string $name
     */
    public function removeAttribute(string $name): void
    {
        $this->attributes->remove($name);
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/getAttribute
     *
     * @uses MutableAttributeMapInterface::get()
     *
     * @param non-empty-string $name
     */
    public function getAttribute(string $name): ?string
    {
        return $this->attributes->get($name);
    }

    /**
     * @api
     *
     * @link https://developer.mozilla.org/docs/Web/API/Element/setAttribute
     *
     * @uses MutableAttributeMapInterface::set()
     *
     * @param non-empty-string $name
     */
    public function setAttribute(string $name, string $value): void
    {
        $this->attributes->set($name, $value);
    }
}
