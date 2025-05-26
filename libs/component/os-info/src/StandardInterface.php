<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

interface StandardInterface extends \Stringable
{
    /**
     * Gets the name of standard.
     *
     * @var non-empty-string
     */
    public string $name {
        get;
    }

    /**
     * Gets the parent standard reference.
     *
     * Returns {@see null} if this standard is a root (has no parent).
     */
    public ?self $parent {
        get;
    }

    /**
     * Checks if this standard supports the given standard.
     */
    public function isSupports(StandardInterface $standard): bool;
}
