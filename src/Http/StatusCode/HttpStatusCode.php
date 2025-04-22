<?php

declare(strict_types=1);

namespace Boson\Http\StatusCode;

use Boson\Http\StatusCode\Category\HttpCategory;

/**
 * @template-extends StandardStatusCode<int<100, 599>>
 */
final readonly class HttpStatusCode extends StandardStatusCode
{
    /**
     * @param int<100, 599> $code
     */
    public function __construct(int $code, string $reason = '')
    {
        parent::__construct($code, $reason, HttpCategory::fromStatusCodeValue($code));
    }
}
