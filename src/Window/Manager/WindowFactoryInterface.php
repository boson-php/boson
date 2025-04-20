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
}
