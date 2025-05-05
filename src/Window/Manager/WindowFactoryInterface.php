<?php

declare(strict_types=1);

namespace Boson\Window\Manager;

use Boson\Window\Window;
use Boson\Window\WindowCreateInfo;

interface WindowFactoryInterface
{
    /**
     * Creates a new application window using passed optional configuration DTO.
     *
     * In case of {@see $defer} is {@see true} the window will be created "lazily"
     * and will only actually launch after it is accessed for the first time.
     */
    public function create(
        WindowCreateInfo $info = new WindowCreateInfo(),
        bool $defer = false,
    ): Window;
}
