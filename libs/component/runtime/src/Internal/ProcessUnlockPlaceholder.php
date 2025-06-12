<?php

declare(strict_types=1);

namespace Boson\Internal;

use Boson\Application;
use Boson\ApplicationPollerInterface;
use Boson\Internal\Saucer\LibSaucer;
use FFI\CData;

/**
 * Provides a placeholder to unlock the process workflow.
 *
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final class ProcessUnlockPlaceholder implements ApplicationPollerInterface
{
    private readonly CData $ptr;

    private ?\Throwable $exception = null;

    public function __construct(
        private readonly LibSaucer $api,
        private readonly Application $app,
    ) {
        $this->ptr = $this->app->id->ptr;
    }

    public function next(): bool
    {
        if ($this->app->isRunning === false) {
            return false;
        }

        if (\Fiber::getCurrent() !== null) {
            \Fiber::suspend($this->app);
        }

        if ($this->exception !== null) {
            [$exception, $this->exception] = [$this->exception, null];

            throw $exception;
        }

        $this->api->saucer_application_run_once($this->ptr);

        return true;
    }

    public function fail(\Throwable $e): void
    {
        $this->exception = $e;
    }
}
