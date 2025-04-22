<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

use Boson\Shared\ValueObject\StringValueObjectInterface;

interface CategoryInterface extends StringValueObjectInterface
{
    /**
     * @var non-empty-string
     */
    public string $name { get; }
}
