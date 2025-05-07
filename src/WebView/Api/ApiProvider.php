<?php

declare(strict_types=1);

namespace Boson\WebView\Api;

use Boson\Dispatcher\Event;
use Boson\Dispatcher\EventDispatcherInterface;
use Boson\Dispatcher\Intention;
use Boson\Dispatcher\Subscription\CancellableSubscriptionInterface;
use Boson\Internal\Saucer\LibSaucer;
use Boson\WebView\WebView;
use FFI\CData;

abstract class ApiProvider
{
    protected readonly CData $ptr;

    public function __construct(
        protected readonly LibSaucer $api,
        protected readonly WebView $webview,
        private readonly EventDispatcherInterface $dispatcher,
    ) {
        $this->ptr = $this->webview->window->id->ptr;
    }

    /**
     * @template TArgEvent of object
     *
     * @param class-string<TArgEvent> $event the event (class) name
     * @param callable(TArgEvent):void $then the listener callback
     *
     * @return CancellableSubscriptionInterface<TArgEvent>
     */
    protected function listen(string $event, callable $then): CancellableSubscriptionInterface
    {
        return $this->webview->events->addEventListener($event, $then);
    }

    /**
     * @param Intention<WebView> $intention
     */
    protected function intent(Intention $intention): bool
    {
        $this->dispatcher->dispatch($intention);

        return $intention->isCancelled === false;
    }

    /**
     * @param Event<WebView> $event
     */
    protected function dispatch(Event $event): void
    {
        $this->dispatcher->dispatch($event);
    }
}
