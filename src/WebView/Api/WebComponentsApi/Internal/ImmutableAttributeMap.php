<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponentsApi\Internal;

use Boson\WebView\Api\WebComponentsApi\Element\AttributeMapInterface;

/**
 * @template-implements \IteratorAggregate<non-empty-string, string>
 */
readonly class ImmutableAttributeMap implements AttributeMapInterface, \IteratorAggregate
{
    /**
     * @param non-empty-string $id
     */
    public function __construct(
        protected ElementInteractor $ctx,
    ) {}

    public function get(string $attribute): ?string
    {
        return $this->ctx->get(\sprintf(
            'getAttribute("%s")',
            \addcslashes($attribute, '"'),
        ));
    }

    public function has(string $attribute): bool
    {
        return $this->ctx->get(\sprintf(
            'getAttribute("%s") !== null',
            \addcslashes($attribute, '"'),
        ));
    }

    public function count(): int
    {
        return (int) $this->ctx->get('attributes.length');
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
