<?php

declare(strict_types=1);

namespace Boson\Component\OsInfo;

use Boson\Contracts\ValueObject\StringValueObjectInterface;

/**
 * @template-extends StringValueObjectInterface<non-empty-string>
 */
interface StandardInterface extends StringValueObjectInterface
{
    /**
     * Gets the name of a standard.
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
    public function is(StandardInterface $standard): bool;
}
