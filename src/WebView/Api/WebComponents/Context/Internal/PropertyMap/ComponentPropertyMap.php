<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal\PropertyMap;

use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\WebComponents\Context\PropertyMapInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
readonly class ComponentPropertyMap implements PropertyMapInterface
{
    public function __construct(
        private SyncDataRetrieverInterface $data,
    ) {}

    public function get(string $property): mixed
    {
        $result = $this->data->get(\sprintf(
            'JSON.stringify(this[`%s`])',
            \addcslashes($property, '`'),
        ));

        if (!\is_string($result)) {
            return $result;
        }

        return \json_decode($result, true, flags: \JSON_THROW_ON_ERROR);
    }

    public function has(string $property): bool
    {
        /** @var bool */
        return $this->data->get(\sprintf(
            'this.hasOwnProperty(`%s`) || this[`%1$s`] !== undefined',
            \addcslashes($property, '`'),
        ));
    }
}
