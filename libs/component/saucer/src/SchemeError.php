<?php

declare(strict_types=1);

namespace Boson\Component\Saucer;

final readonly class SchemeError
{
    public const int SAUCER_SCHEME_ERROR_NOT_FOUND = 404;
    public const int SAUCER_SCHEME_ERROR_INVALID = 400;
    public const int SAUCER_SCHEME_ERROR_DENIED = 401;
    /**
     * @deprecated This constant has been removed
     */
    public const int SAUCER_SCHEME_ERROR_ABORTED = -1;
    public const int SAUCER_SCHEME_ERROR_FAILED = -1;

    private function __construct() {}
}
