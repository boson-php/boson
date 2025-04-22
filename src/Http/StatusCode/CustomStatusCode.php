<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

use Boson\Http\StatusCode\Category\CustomCategory;

/**
 * @template-extends StandardStatusCode<int>
 */
final readonly class CustomStatusCode extends StandardStatusCode
{
    public function __construct(int $code, string $reason = '', ?CategoryInterface $category = null)
    {
        $category ??= new CustomCategory('Unknown');

        parent::__construct($code, $reason, $category);
    }
}
