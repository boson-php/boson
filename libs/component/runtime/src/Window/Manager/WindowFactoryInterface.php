<?php

declare(strict_types=1);

namespace Boson\Window\Manager;

use Boson\Window\Window;
use Boson\Window\WindowCreateInfo;

interface WindowFactoryInterface
{
    /**
     * Creates a new application window using passed optional configuration DTO.
     */
    public function create(WindowCreateInfo $info = new WindowCreateInfo()): Window;

    /**
     * Creates a new application window using passed optional configuration DTO
     * "lazily" and will only actually launch after it is accessed for the
     * first time.
     */
    public function defer(WindowCreateInfo $info = new WindowCreateInfo()): Window;
}
