<?php

declare(strict_types=1);

namespace Boson\WebView\Api\Schemes;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Attribute\AvailableAs;
use Boson\Extension\Extension;
use Boson\WebView\Api\Schemes\Handler\ErrorHandlerInterface;
use Boson\WebView\Api\Schemes\Handler\WhoopsErrorHandler;
use Boson\WebView\WebView;

//
// Note:
// 1) This "$_" assign hack removes these constants from IDE autocomplete.
// 2) Only define-like constants allow object instances.
//
\define($_ = 'Boson\WebView\Api\Schemes\DEFAULT_ERROR_HANDLERS', [
    new WhoopsErrorHandler(),
]);

/**
 * @template-extends Extension<WebView>
 */
#[AvailableAs('schemes', SchemesProviderInterface::class)]
final class SchemesExtension extends Extension
{
    /**
     * @var list<ErrorHandlerInterface>
     *
     * @noinspection PhpUndefinedConstantInspection
     */
    public const array DEFAULT_ERROR_HANDLERS = DEFAULT_ERROR_HANDLERS;

    /**
     * @var list<ErrorHandlerInterface>
     */
    private readonly array $handlers;

    /**
     * @param iterable<mixed, ErrorHandlerInterface> $handlers
     */
    public function __construct(
        iterable $handlers = self::DEFAULT_ERROR_HANDLERS,
    ) {
        $this->handlers = \iterator_to_array($handlers, false);
    }

    public function load(IdentifiableInterface $ctx, EventListener $listener): SchemesProvider
    {
        return new SchemesProvider(
            handlers: $this->handlers,
            context: $ctx,
            listener: $listener,
        );
    }
}
