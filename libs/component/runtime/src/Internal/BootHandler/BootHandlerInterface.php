<?php

declare(strict_types=1);

namespace Boson\Internal\BootHandler;

interface BootHandlerInterface
{
    /**
     * Called every time a new application is created.
     */
    public function boot(): void;
}
