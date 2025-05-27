<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

interface ArchitectureInterface extends \Stringable
{
    /**
     * @var non-empty-string
     */
    public string $name {
        get;
    }
}
