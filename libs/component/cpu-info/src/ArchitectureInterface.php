<?php

declare(strict_types=1);

namespace Boson\Component\CpuInfo;

interface ArchitectureInterface extends \Stringable
{
    /**
     * Gets the name of the CPU architecture.
     *
     * The name should be a non-empty string that uniquely
     * identifies this architecture.
     *
     * @var non-empty-string
     */
    public string $name {
        get;
    }

    /**
     * Gets the parent CPU architecture, if any.
     *
     * Returns {@see null} if this arch is a root architecture (has no parent).
     */
    public ?self $parent {
        get;
    }

    /**
     * Checks if this architecture is the same as or a descendant
     * of the given architecture.
     */
    public function is(ArchitectureInterface $arch): bool;
}
