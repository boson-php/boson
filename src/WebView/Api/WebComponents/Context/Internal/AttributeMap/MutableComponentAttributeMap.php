<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal\AttributeMap;

use Boson\WebView\Api\Data\SyncDataRetrieverInterface;
use Boson\WebView\Api\Scripts\ScriptEvaluatorInterface;
use Boson\WebView\Api\WebComponents\Context\MutableAttributeMapInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
final readonly class MutableComponentAttributeMap extends ComponentAttributeMap implements
    MutableAttributeMapInterface
{
    public function __construct(
        private ScriptEvaluatorInterface $scripts,
        SyncDataRetrieverInterface $data,
    ) {
        parent::__construct($data);
    }

    public function set(string $attribute, string $value): void
    {
        $this->scripts->eval(\sprintf(
            'this.setAttribute(`%s`, `%s`)',
            \addcslashes($attribute, '`'),
            \addcslashes($value, '`'),
        ));
    }

    public function remove(string $attribute): void
    {
        $this->scripts->eval(\sprintf(
            'this.removeAttribute(`%s`)',
            \addcslashes($attribute, '`'),
        ));
    }
}
