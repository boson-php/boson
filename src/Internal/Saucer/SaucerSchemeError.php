<?php

declare(strict_types=1);

namespace Boson\Internal\Saucer;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson
 */
final readonly class SaucerSchemeError
{
    public const int SAUCER_REQUEST_ERROR_NOT_FOUND = 0;
    public const int SAUCER_REQUEST_ERROR_INVALID = 1;
    public const int SAUCER_REQUEST_ERROR_ABORTED = 2;
    public const int SAUCER_REQUEST_ERROR_DENIED = 3;
    public const int SAUCER_REQUEST_ERROR_FAILED = 4;

    private function __construct() {}
}
