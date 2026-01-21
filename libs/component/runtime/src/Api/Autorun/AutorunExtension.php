<?php

declare(strict_types=1);

namespace Boson\Api\Autorun;

use Boson\Api\Autorun\Event\ExpectsAutorun;
use Boson\Application;
use Boson\ApplicationCreateInfo;
use Boson\Contracts\EventListener\Subscription\CancellableSubscriptionInterface;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Event\ApplicationStarted;
use Boson\Extension\Extension;

/**
 * @template-extends Extension<Application>
 */
final class AutorunExtension extends Extension
{
    /**
     * List of loaded applications that have never been launched
     *
     * @var \WeakMap<Application, CancellableSubscriptionInterface>
     */
    private readonly \WeakMap $awaitForRunning;

    public function __construct(
        private readonly AutorunCreateInfo $info = new AutorunCreateInfo(),
    ) {
        $this->awaitForRunning = new \WeakMap();
    }

    public function load(IdentifiableInterface $ctx, EventListener $listener): null
    {
        $this->listenStartup($ctx, $listener);

        $callback = function () use ($ctx, $listener): void {
            $this->onApplicationShouldStart($ctx, $listener);
        };

        foreach ($this->info->handlers as $handler) {
            if ($handler->register($callback)) {
                break;
            }
        }

        return null;
    }

    /**
     * Adds a listener that responds to application startup events.
     */
    private function listenStartup(Application $app, EventListener $listener): void
    {
        // Skip in case of listener already defined
        if (isset($this->awaitForRunning[$app])) {
            return;
        }

        $this->awaitForRunning[$app] = $listener->addEventListener(
            event: ApplicationStarted::class,
            listener: $this->onApplicationStarted(...),
        );
    }

    /**
     * A callback that is called when the application starts
     */
    private function onApplicationStarted(ApplicationStarted $event): void
    {
        $this->shouldNotRunAnymore($event->subject);
    }

    /**
     * A callback that is called when the application WANTS to start (using
     * autorun features).
     */
    private function onApplicationShouldStart(Application $app, EventListener $listener): void
    {
        if (!$this->shouldStart($app)) {
            return;
        }

        $this->shouldNotRunAnymore($app);

        $listener->dispatch($intention = new ExpectsAutorun($app));

        if ($intention->isCancelled) {
            return;
        }

        $app->run();
    }

    /**
     * Returns {@see true} in case of application may start using
     * autorun feature.
     */
    private function shouldStart(Application $app): bool
    {
        return isset($this->awaitForRunning[$app]);
    }

    /**
     * Marks the application as already launched at some point.
     */
    private function shouldNotRunAnymore(Application $app): void
    {
        // Cancel subscription
        ($this->awaitForRunning[$app] ?? null)?->cancel();

        // Remove subscription
        unset($this->awaitForRunning[$app]);
    }
}
