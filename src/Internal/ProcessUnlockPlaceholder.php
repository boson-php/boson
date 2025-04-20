<?php

declare(strict_types=1);

namespace Boson\Internal;

use Boson\Application;
use Boson\Internal\Saucer\LibSaucer;
use FFI\CData;

/**
 * Provides a placeholder to unlock the process workflow.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class ProcessUnlockPlaceholder
{
    private CData $ptr;

    public function __construct(
        private LibSaucer $api,
        private Application $app,
    ) {
        $this->ptr = $this->app->id->ptr;
    }

    public function next(): bool
    {
        if ($this->app->isRunning === false) {
            return false;
        }

        \usleep(1);

        $this->api->saucer_application_run_once($this->ptr);

        return true;
    }
}
