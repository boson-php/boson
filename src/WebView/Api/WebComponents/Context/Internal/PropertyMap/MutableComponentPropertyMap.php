<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal\PropertyMap;

use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\Scripts\ScriptEvaluatorInterface;
use Boson\WebView\Api\WebComponents\Context\MutablePropertyMapInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponent
 */
final readonly class MutableComponentPropertyMap extends ComponentPropertyMap implements
    MutablePropertyMapInterface
{
    public function __construct(
        private ScriptEvaluatorInterface $scripts,
        SyncDataRetrieverInterface $data,
    ) {
        parent::__construct($data);
    }

    public function set(string $property, mixed $value): void
    {
        $this->scripts->eval(\sprintf(
            'this[`%s`] = JSON.parse(%s)',
            \addcslashes($property, '`'),
            \json_encode($value, \JSON_THROW_ON_ERROR),
        ));
    }

    public function remove(string $property): void
    {
        $this->scripts->eval(\sprintf(
            'delete this[`%s`]',
            \addcslashes($property, '`'),
        ));
    }
}
