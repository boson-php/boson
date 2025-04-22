<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode\Category;

use Boson\Http\StatusCode\CategoryInterface;

/**
 * An implementation of any builtin status code category.
 */
abstract readonly class StandardCategory implements CategoryInterface
{
    use CategoryValueObjectImpl;

    public function __construct(
        /**
         * @var non-empty-string
         */
        public string $name,
    ) {}
}
