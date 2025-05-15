<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Internal;

use Boson\WebView\Api\WebComponents\Element\AttributeMapInterface;

/**
 * @template-implements \IteratorAggregate<non-empty-string, string>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponentsApi
 */
readonly class ImmutableAttributeMap implements AttributeMapInterface, \IteratorAggregate
{
    public function __construct(
        protected ElementInteractor $ctx,
    ) {}

    public function get(string $attribute): ?string
    {
        /** @var string|null */
        return $this->ctx->get(\sprintf(
            'getAttribute("%s")',
            \addcslashes($attribute, '"'),
        ));
    }

    public function has(string $attribute): bool
    {
        /** @var bool */
        return $this->ctx->get(\sprintf(
            'getAttribute("%s") !== null',
            \addcslashes($attribute, '"'),
        ));
    }

    public function count(): int
    {
        /** @var int<0, max> */
        return $this->ctx->get('attributes.length');
    }

    public function getIterator(): \Traversable
    {
        /** @var list<array{non-empty-string, string}> $attributes */
        $attributes = (array) $this->ctx->get('[...attributes].map(attr => [attr.name, attr.value])');

        foreach ($attributes as [$name, $value]) {
            yield $name => $value;
        }
    }
}
