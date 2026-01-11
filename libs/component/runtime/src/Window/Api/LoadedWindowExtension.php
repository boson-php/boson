<?php

declare(strict_types=1);

namespace Boson\Window\Api;

use Boson\Api\LoadedApplicationExtension;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Shared\StructPointerId;
use Boson\Window\Exception\WindowApiDereferenceException;
use Boson\Window\Window;

/**
 * @template TContext of IdentifiableInterface<StructPointerId> = Window
 *
 * @template-extends LoadedApplicationExtension<TContext>
 */
abstract class LoadedWindowExtension extends LoadedApplicationExtension
{
    /**
     * Gets reference to the context's ID
     */
    protected StructPointerId $id {
        #[\Override]
        get => $this->window->id;
    }

    protected Window $window {
        get => $this->reference->get()
            ?? throw WindowApiDereferenceException::becauseNoWindow();
    }

    /**
     * @var \WeakReference<Window>
     */
    private readonly \WeakReference $reference;

    public function __construct(
        Window $window,
        EventListener $listener,
    ) {
        $this->reference = \WeakReference::create($window);

        parent::__construct($window->app, $listener);
    }
}
