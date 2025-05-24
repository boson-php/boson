<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal;

use JetBrains\PhpStorm\Language;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\WebView\Api\WebComponents\Context\Internal
 */
abstract readonly class ComponentAwareBuilder
{
    private const string TEMPLATE = <<<'JS'
         (function () {
             try {
                var __%s = window.boson.components.instances.get("%1$s");
                if (__%1$s) {
                    return (function() { %s }).apply(__%1$s);
                }
            } catch (e) {
                console.error(e);
            }
        })();
        JS;

    public function __construct(
        /**
         * @var non-empty-string
         */
        private string $id,
    ) {}

    protected function build(#[Language('JavaScript')] string $code): string
    {
        return \sprintf(self::TEMPLATE, $this->id, $code);
    }
}
