<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal\AttributeMap;

use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\WebComponents\Context\AttributeMapInterface;

/**
 * @template-implements \IteratorAggregate<non-empty-string, string>
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
readonly class ComponentAttributeMap implements AttributeMapInterface, \IteratorAggregate
{
    public function __construct(
        private SyncDataRetrieverInterface $data,
    ) {}

    public function get(string $attribute): ?string
    {
        /** @var string|null */
        return $this->data->get(\sprintf(
            'this.getAttribute(`%s`)',
            \addcslashes($attribute, '`'),
        ));
    }

    public function has(string $attribute): bool
    {
        /** @var bool */
        return $this->data->get(\sprintf(
            'this.getAttribute(`%s`) !== null',
            \addcslashes($attribute, '`'),
        ));
    }

    public function count(): int
    {
        /** @var int<0, max> */
        return $this->data->get('this.attributes.length');
    }

    public function getIterator(): \Traversable
    {
        /** @var list<array{non-empty-string, string}> $attributes */
        $attributes = (array) $this->data->get(
            code: '[...this.attributes].map(attr => [attr.name, attr.value])',
        );

        foreach ($attributes as [$name, $value]) {
            yield $name => $value;
        }
    }
}
