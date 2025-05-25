<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\Exception;

final class ParsingException extends BodyDecoderException
{
    public const int ERROR_CODE_INVALID_BODY = 0x01;
    public const int ERROR_CODE_ENDLESS_BODY = 0x02;

    public static function becauseInvalidBody(?\Throwable $prev = null): self
    {
        return new self('Malformed request body', self::ERROR_CODE_INVALID_BODY, $prev);
    }

    public static function becauseEndlessBody(?\Throwable $prev = null): self
    {
        return new self('Unclosed (endless) request body', self::ERROR_CODE_ENDLESS_BODY, $prev);
    }
}
