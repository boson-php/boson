<?php

declare(strict_types=1);

namespace Boson\Api\QuitOnClose;

use Boson\Application;
use Boson\Contracts\Id\IdentifiableInterface;
use Boson\Dispatcher\EventListener;
use Boson\Extension\Extension;
use Boson\Window\Event\WindowClosed;

/**
 * Controls application shutdown behavior when all windows are closed.
 *
 * @template-extends Extension<Application>
 */
final class QuitOnCloseExtension extends Extension
{
    public function load(IdentifiableInterface $ctx, EventListener $listener): null
    {
        $listener->addEventListener(WindowClosed::class, $this->onWindowClose(...));

        return null;
    }

    /**
     * Check whether the application should quit.
     *
     * @return bool {@see true} when there are no open windows;
     *              {@see false} otherwise
     */
    private function shouldQuit(Application $app): bool
    {
        return $app->windows->count() === 0;
    }

    /**
     * Handle window close: quit when no windows remain.
     */
    private function onWindowClose(WindowClosed $e): void
    {
        $application = $e->subject->app;

        if (!$this->shouldQuit($application)) {
            return;
        }

        $application->quit();
    }
}
