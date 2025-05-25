<?php

declare(strict_types=1);

namespace Boson\Component\Http\Body\MultipartFormData;

use Boson\Contracts\Http\RequestInterface;

/**
 * @internal this is an internal library class, please do not use it in your code
 * @psalm-internal Boson\Component\Http\Body
 */
final readonly class FormDataBoundary
{
    /**
     * @return non-empty-string|null
     */
    public static function findFromRequest(RequestInterface $request): ?string
    {
        $contentType = (string) $request->headers->first('content-type', '');

        // 1. Lookup for ";" of `content-type: multipart/form-data; boundary=xxxx`
        //    header and skip in case of boundary part is missing.
        if (($boundaryPartOffset = \strpos($contentType, ';')) === false) {
            return null;
        }

        // 2. Select all after `multipart/form-data` prefix and skip
        //    in case boundary part is not starts with `boundary=` prefix.
        $boundaryPart = \trim(\substr($contentType, $boundaryPartOffset + 1));

        if (!\str_starts_with($boundaryPart, 'boundary=')) {
            return null;
        }

        // 3. Select all boundary part's value and skip in case of
        //    this value is empty (or contain whitespaces).
        $boundary = \ltrim(\substr($boundaryPart, 9));

        if ($boundary === '') {
            return null;
        }

        return $boundary;
    }

    /**
     * Searches for the final boundary in the given stream and
     * returns the found size for reading.
     *
     * @param resource $stream Reference to resource stream
     * @param string $boundary Expected boundary string
     *
     * @return int<0, max> positive int size of the boundary or 0 instead
     */
    public static function isFinal(mixed $stream, string $boundary): int
    {
        if ($boundary === '') {
            return 0;
        }

        return self::match($stream, "--$boundary--");
    }

    /**
     * Searches for the start boundary in the given stream and
     * returns the found size for reading.
     *
     * @param resource $stream Reference to resource stream
     * @param string $boundary Expected boundary string
     *
     * @return int<0, max> positive int size of the boundary or 0 instead
     */
    public static function isStarted(mixed $stream, string $boundary): int
    {
        if ($boundary === '') {
            return 0;
        }

        return self::match($stream, "--$boundary\r\n");
    }

    /**
     * @param resource $stream
     * @param non-empty-string $expectedValue
     *
     * @return int<0, max>
     */
    private static function match(mixed $stream, string $expectedValue): int
    {
        if (!\is_resource($stream)) {
            return 0;
        }

        $expectedLength = \strlen($expectedValue);

        $actualValue = (string) \fread($stream, $expectedLength);
        $actualLength = \strlen($actualValue);

        if ($actualLength > 0) {
            \fseek($stream, -$actualLength, \SEEK_CUR);
        }

        return $expectedValue === $actualValue ? $actualLength : 0;
    }
}
