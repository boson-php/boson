<?php

declare(strict_types=1);

namespace Boson\Component\Compiler\Assembly;

interface EditionInterface extends \Stringable
{
    /**
     * @var non-empty-string
     */
    public string $name {
        get;
    }

    /**
     * @var list<non-empty-string>
     */
    public array $extensions {
        get;
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string;
}
