<?php

declare(strict_types=1);

namespace Boson\WebView\Api\WebComponents\Context\Internal;

use Boson\WebView\Api\Scripts\ScriptEvaluatorInterface;
use Boson\WebView\Api\ScriptsApiInterface;
use JetBrains\PhpStorm\Language;

/**
 * @internal this is an internal library class, please do not use it in your code.
 * @psalm-internal Boson\WebView\Api\WebComponents
 */
final readonly class ComponentEvaluator extends ComponentAwareBuilder implements
    ScriptEvaluatorInterface
{
    /**
     * @param non-empty-string $id
     */
    public function __construct(
        string $id,
        private ScriptsApiInterface $scripts,
    ) {
        parent::__construct($id);
    }

    public function eval(#[Language('JavaScript')] string $code): void
    {
        $this->scripts->eval($this->build($code));
    }
}
