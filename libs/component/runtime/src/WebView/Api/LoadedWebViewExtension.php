<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Shared\StructPointerId;
use Boson\WebView\Exception\WebViewApiDereferenceException;
use Boson\WebView\WebView;
use Boson\Window\Api\LoadedWindowExtension;

/**
 * @template TContext of IdentifiableInterface<StructPointerId> = WebView
 *
 * @template-extends LoadedWindowExtension<TContext>
 */
abstract class LoadedWebViewExtension extends LoadedWindowExtension
{
    /**
     * Gets reference to the context's ID
     */
    protected StructPointerId $id {
        #[\Override]
        get => $this->webview->id;
    }

    protected WebView $webview {
        get => $this->reference->get()
            ?? throw WebViewApiDereferenceException::becauseNoWebView();
    }

    /**
     * @var \WeakReference<WebView>
     */
    private readonly \WeakReference $reference;

    public function __construct(
        WebView $webview,
        EventListener $listener,
    ) {
        $this->reference = \WeakReference::create($webview);

        parent::__construct($webview->window, $listener);
    }
}
